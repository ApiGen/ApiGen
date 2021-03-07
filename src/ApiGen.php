<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Index\Index;
use Generator;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutputInterface;


final class ApiGen
{
	public function __construct(
		private Analyzer $analyzer,
		private Indexer $indexer,
		private Renderer $renderer,
	) {
	}


	public function generate(ConsoleOutputInterface $output, array $files, string $outputDir): Generator
	{
		$index = new Index();

		$analyzeTime = 0;
		$indexTime = 0;
		$renderTime = 0;

		$analyzeTime -= microtime(true);

		$analyzeProgress = new ProgressBar($output->section());
		$analyzeProgress->setFormat('verbose');

		$indexProgress = new ProgressBar($output->section());
		$renderProgress = new ProgressBar($output->section());

		foreach (yield $this->analyzer->analyze($analyzeProgress, $files) as $info) {
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
		$this->renderer->render($index, $outputDir);
		$renderTime += microtime(true);

		dump(sprintf('Analyze Time:       %6.0f ms', $analyzeTime * 1e3));
		dump(sprintf('Index Time:         %6.0f ms', $indexTime * 1e3));
		dump(sprintf('Render Time:        %6.0f ms', $renderTime * 1e3));
	}
}
