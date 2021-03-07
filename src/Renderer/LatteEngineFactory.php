<?php declare(strict_types = 1);

namespace ApiGenX\Renderer;

use ApiGenX\SourceHighlighter;
use ApiGenX\UrlGenerator;
use Latte;
use League\CommonMark\CommonMarkConverter;


final class LatteEngineFactory
{
	public function __construct(
		private LatteFunctions $functions,
		private UrlGenerator $url,
		private CommonMarkConverter $commonMark,
		private SourceHighlighter $sourceHighlighter,
	) {
	}


	public function create(): Latte\Engine
	{
		$latte = new Latte\Engine();

		$latte->addFunction('asset', [$this->functions, 'asset']);
		$latte->addFunction('shortDescription', [$this->functions, 'shortDescription']);
		$latte->addFunction('elementName', [$this->functions, 'elementName']);
		$latte->addFunction('elementShortDescription', [$this->functions, 'elementShortDescription']);

		$latte->addFunction('namespaceUrl', [$this->url, 'namespace']);
		$latte->addFunction('elementUrl', [$this->url, 'element']); // TODO: rename?


//		$latte->addFilter('staticFile', fn(string $file) => "/src/Templates/Classic/$file"); // TODO!
		$latte->addFilter('relativePath', fn(?string $path) => $path ? $this->url->relative($path) : null); // TODO!
		$latte->addFunction('longDescription', fn(string $description) => new Latte\Runtime\Html($this->commonMark->convertToHtml($description)));
//		$latte->addFilter('groupUrl', fn(string $s) => $s);
//		$latte->addFilter('namespaceUrl', [$this->url, 'namespace']);
//		$latte->addFilter('elementUrl', [$this->url, 'classLike']); // TODO: rename
		$latte->addFilter('sourceUrl', [$this->url, 'source']);
		$latte->addFilter('highlight', [$this->sourceHighlighter, 'highlight']);
//		$latte->addFilter('exprPrint', [$exprPrinter, 'prettyPrintExpr']);
//
//		$latte->addFunction('stripHtml', fn (Latte\Runtime\Html $html) => html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8')); // TODO!

		return $latte;
	}
}
