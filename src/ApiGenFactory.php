<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Renderer\LatteEngineFactory;
use ApiGenX\Renderer\LatteFunctions;
use ApiGenX\Renderer\SourceHighlighter;
use ApiGenX\Renderer\UrlGenerator;
use ApiGenX\TaskExecutor\DefaultTaskEnvironment;
use ApiGenX\TaskExecutor\LimitTaskExecutor;
use ApiGenX\TaskExecutor\PoolTaskExecutor;
use ApiGenX\TaskExecutor\SimpleTaskExecutor;
use ApiGenX\TaskExecutor\WorkerTaskExecutor;
use League;
use PhpParser;
use React;
use React\EventLoop\LoopInterface;


final class ApiGenFactory
{
	public function create(LoopInterface $loop, string $sourceDir, string $baseDir, string $baseUrl, int $workerCount): ApiGen
	{
		$commonMarkEnv = League\CommonMark\Environment::createCommonMarkEnvironment();
		$commonMarkEnv->addExtension(new League\CommonMark\Extension\Autolink\AutolinkExtension());
		$commonMark = new League\CommonMark\CommonMarkConverter([], $commonMarkEnv);

		$urlGenerator = new UrlGenerator($baseDir, $baseUrl);
		$sourceHighlighter = new SourceHighlighter();
		$exprPrettyPrinter = new PhpParser\PrettyPrinter\Standard();

		$latteFunctions = new LatteFunctions($urlGenerator, $sourceHighlighter, $commonMark, $exprPrettyPrinter);
		$latteFactory = new LatteEngineFactory($latteFunctions, $urlGenerator);
		$latte = $latteFactory->create();

		$executor = $workerCount === 1
			? new SimpleTaskExecutor($loop, new DefaultTaskEnvironment())
			: new LimitTaskExecutor(PoolTaskExecutor::create($workerCount, fn() => new WorkerTaskExecutor($loop)), 80);

		$locator = new Locator($sourceDir);
		$analyzer = new Analyzer($locator, $loop, $executor);
		$indexer = new Indexer();
		$renderer = new Renderer($latte, $urlGenerator, $workerCount);

		return new ApiGen($analyzer, $indexer, $renderer);
	}
}
