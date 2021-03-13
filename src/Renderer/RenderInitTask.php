<?php declare(strict_types = 1);

namespace ApiGenX\Renderer;

use ApiGenX\Index\Index;
use ApiGenX\TaskExecutor\Task;
use ApiGenX\TaskExecutor\TaskEnvironment;
use Latte;
use League;


final class RenderInitTask implements Task
{
	public function __construct(
		private Index $index,
		private string $baseDir,
	) {
	}


	public function run(TaskEnvironment $env)
	{
		$env['index'] = $this->index;
		$env['latte'] = $this->createLatte();

		return [];
	}


	private function createLatte(): Latte\Engine
	{
		$commonMarkEnv = League\CommonMark\Environment::createCommonMarkEnvironment();
		$commonMarkEnv->addExtension(new League\CommonMark\Extension\Autolink\AutolinkExtension());
		$commonMark = new League\CommonMark\CommonMarkConverter([], $commonMarkEnv);

		$urlGenerator = new UrlGenerator($this->baseDir);
		$sourceHighlighter = new SourceHighlighter();

		$latteFunctions = new LatteFunctions();
		$latteFactory = new LatteEngineFactory($latteFunctions, $urlGenerator, $commonMark, $sourceHighlighter);
		$latte = $latteFactory->create();

		return $latte;
	}
}
