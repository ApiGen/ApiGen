<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Index\Index;
use Generator;


final class ApiGen
{
	public function __construct(
		private Analyzer $analyzer,
		private Indexer $indexer,
		private Renderer $renderer,
	) {
	}


	public function generate(array $files, callable $autoloader, string $outputDir, int $workerCount): Generator
	{
		$index = new Index();

		$analyzeTime = 0;
		$indexTime = 0;
		$renderTime = 0;

		$analyzeTime -= microtime(true);

		foreach (yield $this->analyzer->analyze($files, $autoloader) as $info) {
			$analyzeTime += microtime(true);
			$indexTime -= microtime(true);

			$this->indexer->indexFile($index, $info->file, $info->primary);
			$this->indexer->indexNamespace($index, $info->name->namespace, $info->name->namespaceLower, $info->primary);
			$this->indexer->indexClassLike($index, $info);

			$indexTime += microtime(true);
			$analyzeTime -= microtime(true);
		}

		$analyzeTime += microtime(true);
		$indexTime -= microtime(true);
		$this->indexer->postProcess($index);
		$indexTime += microtime(true);

		$renderTime -= microtime(true);
		$this->renderer->render($index, $outputDir, $workerCount);
		$renderTime += microtime(true);

		dump(sprintf('Analyze Time:       %6.0f ms', $analyzeTime * 1e3));
		dump(sprintf('Index Time:         %6.0f ms', $indexTime * 1e3));
		dump(sprintf('Render Time:        %6.0f ms', $renderTime * 1e3));
	}
}
