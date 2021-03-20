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


	public function generate(ConsoleOutputInterface $output, array $files, string $outputDir, string $title): void
	{
		$index = new Index();

		$analyzeProgress = new ProgressBar($output->section());
		$analyzeProgress->setFormat('Analyzing: %current%/%max% [%bar%] %percent:3s%%');

		$indexProgress = new ProgressBar($output->section());
		$indexProgress->setFormat('Indexing:  %current%/%max% [%bar%] %percent:3s%%');

		$renderProgress = new ProgressBar($output->section());
		$renderProgress->setFormat('Rendering: %current%/%max% [%bar%] %percent:3s%%');

		$analyzeTime = -microtime(true);
		$classLikeInfos = $this->analyzer->analyze($analyzeProgress, $files);
		$analyzeTime += microtime(true);

		$indexTime = -microtime(true);
		foreach ($indexProgress->iterate($classLikeInfos) as $info) {
			$this->indexer->indexFile($index, $info->file, $info->primary);
			$this->indexer->indexNamespace($index, $info->name->namespace, $info->name->namespaceLower, $info->primary);
			$this->indexer->indexClassLike($index, $info);
		}

		$this->indexer->postProcess($index);
		$indexTime += microtime(true);

		$renderTime = -microtime(true);
		$this->renderer->render($index, $outputDir, $title);
		$renderTime += microtime(true);

		dump(sprintf('Analyze Time:       %6.0f ms', $analyzeTime * 1e3));
		dump(sprintf('Index Time:         %6.0f ms', $indexTime * 1e3));
		dump(sprintf('Render Time:        %6.0f ms', $renderTime * 1e3));
		dump(sprintf('Peak Memory:        %6.0f MB', memory_get_peak_usage() / 1e6));
	}
}
