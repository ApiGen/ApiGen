<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Analyzer\AnalyzeResult;
use ApiGenX\Index\Index;
use Symfony\Component\Console\Helper\TableSeparator;
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
		$analyzeTime = -microtime(true);
		$analyzeResult = $this->analyze($output, $files);
		$analyzeTime += microtime(true);

		$indexTime = -microtime(true);
		$index = $this->index($output, $analyzeResult);
		$indexTime += microtime(true);

		$renderTime = -microtime(true);
		$this->render($output, $index, $outputDir, $title);
		$renderTime += microtime(true);

		$this->performance($output, $analyzeTime, $indexTime, $renderTime);
		$this->finish($output, $analyzeResult);
	}


	private function analyze(SymfonyStyle $output, array $files): AnalyzeResult
	{
		$progressBar = $output->createProgressBar();
		$progressBar->setFormat(' <fg=green>Analyzing</> %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% %message%');

		$analyzeResult = $this->analyzer->analyze($progressBar, $files);

		$progressBar->setMessage('done');
		$progressBar->finish();
		$output->newLine(2);

		return $analyzeResult;
	}


	private function index(SymfonyStyle $output, AnalyzeResult $analyzeResult): Index
	{
		$index = new Index();

		foreach ($analyzeResult->classLike as $info) {
			$this->indexer->indexFile($index, $info->file, $info->primary);
			$this->indexer->indexNamespace($index, $info->name->namespace, $info->name->namespaceLower, $info->primary);
			$this->indexer->indexClassLike($index, $info);
		}

		$this->indexer->postProcess($index);
		return $index;
	}


	private function render(SymfonyStyle $output, Index $index, string $outputDir, string $title): void
	{
		$progressBar = $output->createProgressBar();
		$progressBar->setFormat(' <fg=green>Rendering</> %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% %message%');

		$this->renderer->render($progressBar, $index, $outputDir, $title);

		$progressBar->setMessage('done');
		$progressBar->finish();
		$output->newLine(2);
	}


	private function performance(SymfonyStyle $output, float $analyzeTime, float $indexTime, float $renderTime): void
	{
		if ($output->isDebug()) {
			$output->definitionList(
				'Performance',
				new TableSeparator(),
				['Analyze Time' => sprintf('%6.0f ms', $analyzeTime * 1e3)],
				['Index Time' => sprintf('%6.0f ms', $indexTime * 1e3)],
				['Render Time' => sprintf('%6.0f ms', $renderTime * 1e3)],
				['Peak Memory' => sprintf('%6.0f MB', memory_get_peak_usage() / 1e6)],
			);
		}
	}


	private function finish(SymfonyStyle $output, AnalyzeResult $analyzeResult): void
	{
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
