<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte;

use ApiGenX\Renderer\SourceHighlighter;
use ApiGenX\Renderer\UrlGenerator;
use League;


final class LatteRendererFactory
{
	public function create(string $baseDir, string $baseUrl, int $workerCount): LatteRenderer
	{
		$commonMark = new League\CommonMark\GithubFlavoredMarkdownConverter();

		$urlGenerator = new UrlGenerator($baseDir, $baseUrl);
		$sourceHighlighter = new SourceHighlighter();

		$latteFunctions = new LatteFunctions($urlGenerator, $sourceHighlighter, $commonMark);
		$latteFactory = new LatteEngineFactory($latteFunctions, $urlGenerator);
		$latte = $latteFactory->create();

		return new LatteRenderer($latte, $urlGenerator, $workerCount);
	}
}
