<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte;

use ApiGenX\Helpers;
use ApiGenX\Index\FileIndex;
use ApiGenX\Index\Index;
use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Renderer;
use ApiGenX\Renderer\Latte\Template\ClassLikeTemplate;
use ApiGenX\Renderer\Latte\Template\GlobalParameters;
use ApiGenX\Renderer\Latte\Template\IndexTemplate;
use ApiGenX\Renderer\Latte\Template\NamespaceTemplate;
use ApiGenX\Renderer\Latte\Template\SourceTemplate;
use ApiGenX\Renderer\Latte\Template\TreeTemplate;
use ApiGenX\Renderer\UrlGenerator;
use Latte;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use Symfony\Component\Console\Helper\ProgressBar;

use function array_filter;
use function array_key_first;
use function basename;
use function count;
use function dirname;
use function extension_loaded;
use function lcfirst;
use function pcntl_fork;
use function pcntl_waitpid;
use function pcntl_wexitstatus;
use function pcntl_wifexited;
use function pcntl_wifsignaled;
use function pcntl_wtermsig;
use function strlen;
use function substr;

use const PHP_SAPI;


final class LatteRenderer implements Renderer
{
	public function __construct(
		private Latte\Engine $latte,
		private UrlGenerator $urlGenerator,
		private int $workerCount,
		private string $title,
		private string $outputDir,
		private ?string $templatesDir = null,
	) {
	}


	public function render(ProgressBar $progressBar, Index $index): void
	{
		FileSystem::delete($this->outputDir);
		FileSystem::createDir($this->outputDir);

		$templatesDir = $this->templatesDir ?? __DIR__ . '/Template';
		$assetsDir = "$templatesDir/assets";
		foreach (Finder::findFiles()->from($assetsDir) as $path => $_) {
			$assetName = substr($path, strlen($assetsDir) + 1);
			$assetPath = $this->urlGenerator->getAssetPath($assetName);
			FileSystem::copy($path, "$this->outputDir/$assetPath");
		}

		$primaryFiles = array_filter($index->files, fn(FileIndex $file) => $file->primary);
		$progressBar->setMaxSteps(2 + count($index->namespace) + count($index->classLike) + count($primaryFiles));

		$this->renderTemplate($progressBar, $this->urlGenerator->getIndexPath(), new IndexTemplate(
			global: new GlobalParameters(
				index: $index,
				title: $this->title,
				activePage: 'index',
				activeNamespace: null,
				activeClassLike: null,
			),
		));

		$this->renderTemplate($progressBar, $this->urlGenerator->getTreePath(), new TreeTemplate(
			global: new GlobalParameters(
				index: $index,
				title: $this->title,
				activePage: 'tree',
				activeNamespace: null,
				activeClassLike: null,
			),
		));

		$this->forkLoop($progressBar, $index->namespace, function (?ProgressBar $progressBar, NamespaceIndex $info) use ($index) {
			$this->renderTemplate($progressBar, $this->urlGenerator->getNamespacePath($info), new NamespaceTemplate(
				global: new GlobalParameters(
					index: $index,
					title: $this->title,
					activePage: 'namespace',
					activeNamespace: $info,
					activeClassLike: null,
				),
				namespace: $info,
			));
		});

		$this->forkLoop($progressBar, $index->classLike, function (?ProgressBar $progressBar, ClassLikeInfo $info) use ($index) {
			$this->renderTemplate($progressBar, $this->urlGenerator->getClassLikePath($info), new ClassLikeTemplate(
				global: new GlobalParameters(
					index: $index,
					title: $this->title,
					activePage: 'classLike',
					activeNamespace: $index->namespace[$info->name->namespaceLower],
					activeClassLike: $info,
				),
				classLike: $info,
			));
		});

		$this->forkLoop($progressBar, $primaryFiles, function (?ProgressBar $progressBar, FileIndex $file, string $path) use ($index) {
			$activeClassLike = $file->classLike ? $file->classLike[array_key_first($file->classLike)] : null;
			$activeNamespace = $activeClassLike ? $index->namespace[$activeClassLike->name->namespaceLower] : null;

			$this->renderTemplate($progressBar, $this->urlGenerator->getSourcePath($path), new SourceTemplate(
				global: new GlobalParameters(
					index: $index,
					title: $this->title,
					activePage: 'source',
					activeNamespace: $activeNamespace,
					activeClassLike: $activeClassLike,
				),
				path: $path,
				source: FileSystem::read($path),
			));
		});
	}


	private function renderTemplate(?ProgressBar $progressBar, string $outputPath, object $template): void
	{
		if ($progressBar !== null) {
			$progressBar->setMessage($outputPath);
			$progressBar->advance();
		}

		$classPath = Helpers::classLikePath($template::class);
		$fileName = lcfirst(basename($classPath, 'Template.php')) . '.latte';

		$templatesDir = $this->templatesDir ?? __DIR__ . '/Template';
		$lattePath = "$templatesDir/pages/$fileName";
		FileSystem::write("$this->outputDir/$outputPath", $this->latte->renderToString($lattePath, $template));
	}


	/**
	 * @template K
	 * @template V
	 *
	 * @param iterable<K, V>                     $it
	 * @param callable(?ProgressBar, V, K): void $handle
	 */
	private function forkLoop(ProgressBar $progressBar, iterable $it, callable $handle): void
	{
		$workerCount = PHP_SAPI === 'cli' && extension_loaded('pcntl') ? $this->workerCount : 1;

		$workers = [];
		$workerId = 0;

		for ($i = 1; $i < $workerCount; $i++) {
			$pid = pcntl_fork();

			if ($pid < 0) {
				throw new \RuntimeException('Failed to fork process, try running ApiGen with --workers 1');

			} elseif ($pid === 0) {
				$workerId = $i;
				$progressBar = null;
				break;

			} else {
				$workers[] = $pid;
			}
		}

		$index = 0;
		foreach ($it as $key => $value) {
			if ((($index++) % $workerCount) === $workerId) {
				$handle($progressBar, $value, $key);

			} elseif ($progressBar !== null) {
				$progressBar->advance();
			}
		}

		if ($workerId !== 0) {
			exit;
		}

		foreach ($workers as $pid) {
			pcntl_waitpid($pid, $status);

			if (pcntl_wifexited($status)) {
				$exitCode = pcntl_wexitstatus($status);
				if ($exitCode !== 0) {
					throw new \RuntimeException("Worker with PID $pid exited with code $exitCode, try running ApiGen with --workers 1");
				}

			} elseif (pcntl_wifsignaled($status)) {
				$signal = pcntl_wtermsig($status);
				throw new \RuntimeException("Worker with PID $pid was killed by signal $signal, try running ApiGen with --workers 1");

			} else {
				throw new \LogicException('Invalid worker state');
			}
		}
	}
}
