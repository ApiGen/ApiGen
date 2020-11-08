<?php declare(strict_types = 1);

namespace ApiGenX;

use Amp;
use Amp\Parallel\Worker\BasicEnvironment;
use Amp\Parallel\Worker\DefaultPool;
use ApiGenX\Info\ClassInfo;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Tasks\AnalyzeTaskX;


final class ApiGen
{
	/** @var Indexer */
	private Indexer $indexer;

	/** @var Renderer */
	private Renderer $renderer;

	/** @var Index */
	private Index $index;


	public function __construct(Indexer $indexer, Renderer $renderer)
	{
		$this->indexer = $indexer;
		$this->renderer = $renderer;
		$this->index = new Index();
	}


	/**
	 * @param string[]                                 $files      indexed by []
	 * @param callable(string $classLikeName): ?string $autoloader
	 */
	public function analyze(array $files, callable $autoloader)
	{
		$async = false;

		$env = new BasicEnvironment();
		$pool = new DefaultPool();

		$promises = [];
		$missing = [];

		foreach ($files as $file) {
			$file = realpath($file);
			$task = new AnalyzeTaskX($file, true);
			$promises[$file] ??= $async ? $pool->enqueue($task) : new Amp\Success($task->run($env));
		}

		while (current($promises) !== false) {
			$dependencies = [];

			while (($promise = current($promises)) !== false) {
				next($promises);

				foreach (Amp\Promise\wait($promise) as $info) {
					foreach ($info->dependencies as $dependency => $_) {
						if (!isset($this->index->classLike[$dependency])) {
							$missing[$dependency][] = $info;
							$dependencies[$dependency][] = $info;
						}
					}

					if ($info instanceof ClassLikeInfo) {
						unset($dependencies[$info->nameLower], $missing[$info->nameLower]);
						$this->indexer->indexFile($this->index, $info->file, $info->primary);
						$this->indexer->indexNamespace($this->index, $info->namespace, $info->namespaceLower);
						$this->indexer->indexClassLike($this->index, $info);
					}
				}
			}

			foreach ($dependencies as $dependency => $dependentClassLikes) {
				$file = $autoloader($dependency);

				if ($file !== null) {
					$file = realpath($file);
					$task = new AnalyzeTaskX($file, false);
					$promises[$file] ??= $async ? $pool->enqueue($task) : new Amp\Success($task->run($env));
				}
			}
		}

		foreach ($missing as $dependency => $dependentClassLikes) {
			dump(["MISSING: $dependency" => $dependentClassLikes[0]->name]);

			$info = new ClassInfo($dependency); // TODO: mark as missing
			$info->primary = false;
			$this->indexer->indexFile($this->index, $info->file, $info->primary);
			$this->indexer->indexNamespace($this->index, $info->namespace, $info->namespaceLower);
			$this->indexer->indexClassLike($this->index, $info);
		}

		$this->indexer->postProcess($this->index);
	}


	public function render(string $outputDir)
	{
		$this->renderer->render($this->index, $outputDir);
	}
}
