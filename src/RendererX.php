<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Index\FileIndex;
use ApiGenX\Index\Index;
use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Renderer\RenderClassLikePageTask;
use ApiGenX\Renderer\RenderInitTask;
use ApiGenX\Renderer\RenderTask;
use ApiGenX\Renderer\UrlGenerator;
use ApiGenX\TaskExecutor\TaskExecutor;
use ApiGenX\Templates\ClassicX\ClassLikeTemplate;
use ApiGenX\Templates\ClassicX\GlobalParameters;
use ApiGenX\Templates\ClassicX\IndexTemplate;
use ApiGenX\Templates\ClassicX\NamespaceTemplate;
use ApiGenX\Templates\ClassicX\SourceTemplate;
use ApiGenX\Templates\ClassicX\TreeTemplate;
use Generator;
use Nette\Utils\FileSystem;
use React\Promise\PromiseInterface;
use function React\Promise\all;
use function React\Promise\resolve;


final class RendererX
{
	public function __construct(
		private UrlGenerator $urlGenerator,
		private TaskExecutor $taskExecutor,
		private string $baseDir,
	) {
	}


	public function render(Index $index, string $outputDir, string $title): Generator
	{
		$templateDir = __DIR__ . '/Templates/ClassicX';
		FileSystem::delete($outputDir);
		FileSystem::createDir($outputDir);
		FileSystem::copy("$templateDir/assets", "$outputDir/assets");

		yield $this->taskExecutor->process(new RenderInitTask($index, $this->baseDir));

//		yield $this->renderTemplate("$outputDir/{$this->urlGenerator->index()}", new IndexTemplate(
//			global: new GlobalParameters(
//				title: $title,
//				activePage: 'index',
//				activeNamespace: null,
//				activeClassLike: null,
//			),
//		));
//
//		yield $this->renderTemplate("$outputDir/{$this->urlGenerator->tree()}", new TreeTemplate(
//			global: new GlobalParameters(
//				title: $title,
//				activePage: 'tree',
//				activeNamespace: null,
//				activeClassLike: null,
//			),
//		));

//		yield $this->loop($index->namespace, function (NamespaceIndex $info) use ($outputDir, $index, $title) {
//			return $this->renderTemplate("$outputDir/{$this->urlGenerator->namespace($info)}", new NamespaceTemplate(
//				global: new GlobalParameters(
//					title: $title,
//					activePage: 'namespace',
//					activeNamespace: $info,
//					activeClassLike: null,
//				),
//				namespace: $info,
//			));
//		});

		foreach ($index->classLike as $classLikeName => $info) {
			yield $this->taskExecutor->process(new RenderClassLikePageTask(
				$classLikeName,
				"$outputDir/{$this->urlGenerator->classLike($info)}"
			));
		}

//		yield $this->loop($index->classLike, function (ClassLikeInfo $info) use ($outputDir, $index, $title) {
//			return $this->renderTemplate("$outputDir/{$this->urlGenerator->classLike($info)}", new ClassLikeTemplate(
//				global: new GlobalParameters(
//					title: $title,
//					activePage: 'classLike',
//					activeNamespace: null,//$index->namespace[$info->name->namespaceLower],
//					activeClassLike: $info,
//				),
//				classLike: $info,
//			));
//		});

//		yield $this->loop($index->files, function (FileIndex $file, $path) use ($outputDir, $index, $title) {
//			if (!$file->primary) {
//				return resolve();
//			}
//
//			$activeClassLike = $file->classLike ? $file->classLike[array_key_first($file->classLike)] : null;
//			$activeNamespace = $activeClassLike ? $index->namespace[$activeClassLike->name->namespaceLower] : null;
//
//			return $this->renderTemplate("$outputDir/{$this->urlGenerator->source($path)}", new SourceTemplate(
//				global: new GlobalParameters(
//					title: $title,
//					activePage: 'source',
//					activeNamespace: $activeNamespace,
//					activeClassLike: $activeClassLike,
//				),
//				path: $path,
//				source: FileSystem::read($path),
//			));
//		});
	}


	private function renderTemplate(string $outputPath, object $template): PromiseInterface
	{
		$classPath = (new \ReflectionClass($template))->getFileName();
		$lattePath = dirname($classPath) . '/' . basename($classPath, 'Template.php') . '.latte';

		return $this->taskExecutor->process(new RenderTask($this->baseDir, $lattePath, $template, $outputPath));
	}


	private function loop(iterable $it, callable $handle): Generator
	{
		foreach ($it as $key => $value) {
			echo "$key\n";
			yield $handle($value, $key);
		}
	}


//	private function loop(iterable $it, callable $handle): PromiseInterface
//	{
//		$children = [];
//
//		foreach ($it as $key => $value) {
//			$children[] = $handle($value, $key);
//		}
//
//		return all($children);
//	}
}
