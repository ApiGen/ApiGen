<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte;

use ApiGenX\Helpers;
use ApiGenX\Index\FileIndex;
use ApiGenX\Index\Index;
use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Renderer;
use ApiGenX\Renderer\UrlGenerator;
use ApiGenX\Renderer\Latte\Template\ClassLikeTemplate;
use ApiGenX\Renderer\Latte\Template\GlobalParameters;
use ApiGenX\Renderer\Latte\Template\IndexTemplate;
use ApiGenX\Renderer\Latte\Template\NamespaceTemplate;
use ApiGenX\Renderer\Latte\Template\SourceTemplate;
use ApiGenX\Renderer\Latte\Template\TreeTemplate;
use Latte;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use Symfony\Component\Console\Helper\ProgressBar;


final class LatteRenderer implements Renderer
{
	public function __construct(
		private Latte\Engine $latte,
		private UrlGenerator $urlGenerator,
		private int $workerCount,
	) {
	}


	public function render(ProgressBar $progressBar, Index $index, string $outputDir, string $title): void
	{
		$templateDir = __DIR__ . '/Template';
		$assetsDir = $templateDir . '/assets';

		FileSystem::delete($outputDir);
		FileSystem::createDir($outputDir);

		foreach (Finder::findFiles()->from($assetsDir) as $path => $_) {
			$name = substr($path, strlen($assetsDir) + 1);
			$target = "$outputDir/" . $this->urlGenerator->getAssetPath($name);
			FileSystem::copy($path, $target);
		}

		$primaryFiles = array_filter($index->files, fn(FileIndex $file) => $file->primary);
		$progressBar->setMaxSteps(2 + count($index->namespace) + count($index->classLike) + count($primaryFiles));

		$this->renderTemplate($progressBar, "$outputDir/" . $this->urlGenerator->getIndexPath(), new IndexTemplate(
			global: new GlobalParameters(
				index: $index,
				title: $title,
				activePage: 'index',
				activeNamespace: null,
				activeClassLike: null,
			),
		));

		$this->renderTemplate($progressBar, "$outputDir/" . $this->urlGenerator->getTreePath(), new TreeTemplate(
			global: new GlobalParameters(
				index: $index,
				title: $title,
				activePage: 'tree',
				activeNamespace: null,
				activeClassLike: null,
			),
		));

		$this->forkLoop($progressBar, $index->namespace, function (?ProgressBar $progressBar, NamespaceIndex $info) use ($outputDir, $index, $title) {
			$this->renderTemplate($progressBar, "$outputDir/" . $this->urlGenerator->getNamespacePath($info), new NamespaceTemplate(
				global: new GlobalParameters(
					index: $index,
					title: $title,
					activePage: 'namespace',
					activeNamespace: $info,
					activeClassLike: null,
				),
				namespace: $info,
			));
		});

		$this->forkLoop($progressBar, $index->classLike, function (?ProgressBar $progressBar, ClassLikeInfo $info) use ($outputDir, $index, $title) {
			$this->renderTemplate($progressBar, "$outputDir/" . $this->urlGenerator->getClassLikePath($info), new ClassLikeTemplate(
				global: new GlobalParameters(
					index: $index,
					title: $title,
					activePage: 'classLike',
					activeNamespace: $index->namespace[$info->name->namespaceLower],
					activeClassLike: $info,
				),
				classLike: $info,
			));
		});

		$this->forkLoop($progressBar, $primaryFiles, function (?ProgressBar $progressBar, FileIndex $file, string $path) use ($outputDir, $index, $title) {
			$activeClassLike = $file->classLike ? $file->classLike[array_key_first($file->classLike)] : null;
			$activeNamespace = $activeClassLike ? $index->namespace[$activeClassLike->name->namespaceLower] : null;

			$this->renderTemplate($progressBar, "$outputDir/" . $this->urlGenerator->getSourcePath($path), new SourceTemplate(
				global: new GlobalParameters(
					index: $index,
					title: $title,
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
		$lattePath = dirname($classPath) . '/' . basename($classPath, 'Template.php') . '.latte';
		FileSystem::write($outputPath, $this->latte->renderToString($lattePath, $template));
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
				throw new \RuntimeException();

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
		}
	}
}
