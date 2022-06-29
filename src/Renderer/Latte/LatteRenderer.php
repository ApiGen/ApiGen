<?php declare(strict_types = 1);

namespace ApiGen\Renderer\Latte;

use ApiGen\Index\FileIndex;
use ApiGen\Index\Index;
use ApiGen\Index\NamespaceIndex;
use ApiGen\Info\ClassLikeInfo;
use ApiGen\Info\FunctionInfo;
use ApiGen\Renderer;
use ApiGen\Renderer\Latte\Template\ClassLikeTemplate;
use ApiGen\Renderer\Latte\Template\ConfigParameters;
use ApiGen\Renderer\Latte\Template\FunctionTemplate;
use ApiGen\Renderer\Latte\Template\IndexTemplate;
use ApiGen\Renderer\Latte\Template\LayoutParameters;
use ApiGen\Renderer\Latte\Template\NamespaceTemplate;
use ApiGen\Renderer\Latte\Template\SourceTemplate;
use ApiGen\Renderer\Latte\Template\TreeTemplate;
use ApiGen\Renderer\UrlGenerator;
use Latte;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use ReflectionClass;
use Symfony\Component\Console\Helper\ProgressBar;

use function array_filter;
use function array_key_first;
use function count;
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


class LatteRenderer implements Renderer
{
	public function __construct(
		protected Latte\Engine $latte,
		protected UrlGenerator $urlGenerator,
		protected int $workerCount,
		protected string $title,
		protected string $version,
		protected string $outputDir,
	) {
	}


	public function render(ProgressBar $progressBar, Index $index): void
	{
		FileSystem::delete($this->outputDir);
		FileSystem::createDir($this->outputDir);
		$this->copyAssets();

		$primaryFiles = array_filter($index->files, fn(FileIndex $file) => $file->primary);
		$progressBar->setMaxSteps(2 + count($index->namespace) + count($index->classLike) + count($index->function) + count($primaryFiles));

		$configParameters = new ConfigParameters(
			title: $this->title,
			version: $this->version,
		);

		$this->renderTemplate($progressBar, $this->urlGenerator->getIndexPath(), new IndexTemplate(
			index: $index,
			config: $configParameters,
			layout: new LayoutParameters(activePage: 'index', activeNamespace: null, activeElement: null),
		));

		$this->renderTemplate($progressBar, $this->urlGenerator->getTreePath(), new TreeTemplate(
			index: $index,
			config: $configParameters,
			layout: new LayoutParameters(activePage: 'tree', activeNamespace: null, activeElement: null),
		));

		$this->forkLoop($progressBar, $index->namespace, function (?ProgressBar $progressBar, NamespaceIndex $info) use ($configParameters, $index) {
			$this->renderTemplate($progressBar, $this->urlGenerator->getNamespacePath($info), new NamespaceTemplate(
				index: $index,
				config: $configParameters,
				layout: new LayoutParameters('namespace', $info, activeElement: null),
				namespace: $info,
			));
		});

		$this->forkLoop($progressBar, $index->classLike, function (?ProgressBar $progressBar, ClassLikeInfo $info) use ($configParameters, $index) {
			$activeNamespace = $index->namespace[$info->name->namespaceLower];

			$this->renderTemplate($progressBar, $this->urlGenerator->getClassLikePath($info), new ClassLikeTemplate(
				index: $index,
				config: $configParameters,
				layout: new LayoutParameters('classLike', $activeNamespace, $info),
				classLike: $info,
			));
		});

		$this->forkLoop($progressBar, $index->function, function (?ProgressBar $progressBar, FunctionInfo $info) use ($configParameters, $index) {
			$activeNamespace = $index->namespace[$info->name->namespaceLower];

			$this->renderTemplate($progressBar, $this->urlGenerator->getFunctionPath($info), new FunctionTemplate(
				index: $index,
				config: $configParameters,
				layout: new LayoutParameters('function', $activeNamespace, $info),
				function: $info,
			));
		});

		$this->forkLoop($progressBar, $primaryFiles, function (?ProgressBar $progressBar, FileIndex $file, string $path) use ($configParameters, $index) {
			$activeElement = $file->classLike[array_key_first($file->classLike)] ?? $file->function[array_key_first($file->function)] ?? null;
			$activeNamespace = $activeElement ? $index->namespace[$activeElement->name->namespaceLower] : null;

			$this->renderTemplate($progressBar, $this->urlGenerator->getSourcePath($path), new SourceTemplate(
				index: $index,
				config: $configParameters,
				layout: new LayoutParameters('source', $activeNamespace, $activeElement),
				path: $path,
				source: FileSystem::read($path),
			));
		});
	}


	protected function copyAssets(): void
	{
		$assetsDir = __DIR__ . '/Template/assets';
		foreach (Finder::findFiles()->from($assetsDir) as $path => $_) {
			$assetName = substr($path, strlen($assetsDir) + 1);
			$assetPath = $this->urlGenerator->getAssetPath($assetName);
			FileSystem::copy($path, "$this->outputDir/$assetPath");
		}
	}


	protected function renderTemplate(?ProgressBar $progressBar, string $outputPath, object $template): void
	{
		if ($progressBar !== null) {
			$progressBar->setMessage($outputPath);
			$progressBar->advance();
		}

		$className = (new ReflectionClass($template))->getShortName();
		$lattePath = 'pages/' . lcfirst(substr($className, 0, -8)) . '.latte';
		FileSystem::write("$this->outputDir/$outputPath", $this->latte->renderToString($lattePath, $template));
	}


	/**
	 * @template K
	 * @template V
	 *
	 * @param iterable<K, V>                     $it
	 * @param callable(?ProgressBar, V, K): void $handle
	 */
	protected function forkLoop(ProgressBar $progressBar, iterable $it, callable $handle): void
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
