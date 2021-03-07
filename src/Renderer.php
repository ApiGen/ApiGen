<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Index\FileIndex;
use ApiGenX\Index\Index;
use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Renderer\UrlGenerator;
use ApiGenX\Templates\ClassicX\ClassLikeTemplate;
use ApiGenX\Templates\ClassicX\GlobalParameters;
use ApiGenX\Templates\ClassicX\IndexTemplate;
use ApiGenX\Templates\ClassicX\NamespaceTemplate;
use ApiGenX\Templates\ClassicX\SourceTemplate;
use ApiGenX\Templates\ClassicX\TreeTemplate;
use Latte;
use Nette\Utils\FileSystem;


final class Renderer
{
	public function __construct(
		private Latte\Engine $latte,
		private UrlGenerator $urlGenerator,
		private int $workerCount,
	) {
	}


	public function render(Index $index, string $outputDir, string $title)
	{
		$templateDir = __DIR__ . '/Templates/ClassicX';
		FileSystem::delete($outputDir);
		FileSystem::createDir($outputDir);
		FileSystem::copy("$templateDir/assets", "$outputDir/assets");


		$this->renderTemplate("$outputDir/{$this->urlGenerator->index()}", new IndexTemplate(
			global: new GlobalParameters(
				index: $index,
				title: $title,
				activePage: 'index',
				activeNamespace: null,
				activeClassLike: null,
			),
		));

		$this->renderTemplate("$outputDir/{$this->urlGenerator->tree()}", new TreeTemplate(
			global: new GlobalParameters(
				index: $index,
				title: $title,
				activePage: 'tree',
				activeNamespace: null,
				activeClassLike: null,
			),
		));

		$this->forkLoop($index->namespace, function (NamespaceIndex $info) use ($outputDir, $index, $title) {
			$this->renderTemplate("$outputDir/{$this->urlGenerator->namespace($info)}", new NamespaceTemplate(
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

		$this->forkLoop($index->classLike, function (ClassLikeInfo $info) use ($outputDir, $index, $title) {
			$this->renderTemplate("$outputDir/{$this->urlGenerator->classLike($info)}", new ClassLikeTemplate(
					global: new GlobalParameters(
					index: $index,
					title: $title,
					activePage: 'namespace',
					activeNamespace: $index->namespace[$info->name->namespaceLower],
					activeClassLike: $info,
				),
				classLike: $info,
			));
		});

		$this->forkLoop($index->files, function (FileIndex $file, $path) use ($outputDir, $index, $title) {
			if (!$file->primary) {
				return;
			}

			$activeClassLike = $file->classLike ? $file->classLike[array_key_first($file->classLike)] : null;
			$activeNamespace = $activeClassLike ? $index->namespace[$activeClassLike->name->namespaceLower] : null;

			$this->renderTemplate("$outputDir/{$this->urlGenerator->source($path)}", new SourceTemplate(
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


	private function renderTemplate(string $outputPath, object $template): void
	{
		$classPath = (new \ReflectionClass($template))->getFileName();
		$lattePath = dirname($classPath) . '/' . basename($classPath, 'Template.php') . '.latte';
		FileSystem::write($outputPath, $this->latte->renderToString($lattePath, $template));
	}


	private function forkLoop(iterable $it, callable $handle)
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
				break;

			} else {
				$workers[] = $pid;
			}
		}

		$index = 0;
		foreach ($it as $key => $value) {
			if ((($index++) % $workerCount) === $workerId) {
				$handle($value, $key);
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
