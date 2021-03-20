<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Index\Index;
use Symfony\Component\Console\Style\SymfonyStyle;


final class ApiGen
{
	public function __construct(
		private Analyzer $analyzer,
		private Indexer $indexer,
		private Renderer $renderer,
	) {
	}


	public function generate(SymfonyStyle $output, array $files, string $outputDir, string $title): void
	{
		$index = new Index();

		$analyzeProgress = $output->createProgressBar();
		$analyzeProgress->setFormat('Analyzing: %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% %message%');

		$indexProgress = $output->createProgressBar();
		$indexProgress->setFormat('Indexing:  %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%');

		$renderProgress = $output->createProgressBar();
		$renderProgress->setFormat('Rendering: %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% %message%');

		$analyzeTime = -microtime(true);
		$analyzeResult = $this->analyzer->analyze($analyzeProgress, $files);
		$analyzeProgress->finish();
		$output->newLine();
		$analyzeTime += microtime(true);

		$indexTime = -microtime(true);
		foreach ($indexProgress->iterate($analyzeResult->classLike) as $info) {
			$this->indexer->indexFile($index, $info->file, $info->primary);
			$this->indexer->indexNamespace($index, $info->name->namespace, $info->name->namespaceLower, $info->primary);
			$this->indexer->indexClassLike($index, $info);
		}

		$this->indexer->postProcess($index);
		$indexProgress->finish();
		$output->newLine();
		$indexTime += microtime(true);

		$renderTime = -microtime(true);
		$this->renderer->render($renderProgress, $index, $outputDir, $title);
		$renderProgress->finish();
		$output->newLine();
		$renderTime += microtime(true);

		$output->info(implode("\n", [
			sprintf('Analyze Time:       %6.0f ms', $analyzeTime * 1e3),
			sprintf('Index Time:         %6.0f ms', $indexTime * 1e3),
			sprintf('Render Time:        %6.0f ms', $renderTime * 1e3),
			sprintf('Peak Memory:        %6.0f MB', memory_get_peak_usage() / 1e6),
		]));

		if (!$analyzeResult->error) {
			$output->success('Finished OK');
			return;
		}

		foreach ($analyzeResult->error as $errorKind => $errorGroup) {
			$errorLines = array_column($errorGroup, 'message');

			if (!$output->isVerbose() && count($errorLines) > 5) {
				$errorLines = array_slice($errorLines, 0, 5);
				$errorLines[] = '...';
				$errorLines[] = sprintf('and %d more (use --verbose to show all)', count($errorGroup) - 5);
			}

			$output->error(implode("\n\n", [sprintf('%dx %s:', count($errorGroup), $errorKind), ...$errorLines]));
		}

		$output->warning('Finished with errors');
	}
}
