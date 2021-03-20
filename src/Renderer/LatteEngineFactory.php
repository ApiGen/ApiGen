<?php declare(strict_types = 1);

namespace ApiGenX\Renderer;

use Latte;
use Throwable;


final class LatteEngineFactory
{
	public function __construct(
		private LatteFunctions $functions,
		private UrlGenerator $url,
	) {
	}


	public function create(): Latte\Engine
	{
		$latte = new Latte\Engine();
		$latte->setExceptionHandler(fn(Throwable $e) => throw $e);

		$latte->addFunction('stripHtml', [$this->functions, 'stripHtml']);
		$latte->addFunction('highlight', [$this->functions, 'highlight']);
		$latte->addFunction('exprPrint', [$this->functions, 'prettyPrintExpr']);
		$latte->addFunction('shortDescription', [$this->functions, 'shortDescription']);
		$latte->addFunction('longDescription', [$this->functions, 'longDescription']);

		$latte->addFunction('elementName', [$this->functions, 'elementName']);
		$latte->addFunction('elementShortDescription', [$this->functions, 'elementShortDescription']);
		$latte->addFunction('elementUrl', [$this->functions, 'elementUrl']);

		$latte->addFunction('relativePath', [$this->url, 'getRelativePath']);
		$latte->addFunction('assetUrl', [$this->url, 'getAssetUrl']);
		$latte->addFunction('indexUrl', [$this->url, 'getIndexUrl']);
		$latte->addFunction('treeUrl', [$this->url, 'getTreeUrl']);
		$latte->addFunction('namespaceUrl', [$this->url, 'getNamespaceUrl']);
		$latte->addFunction('classLikeUrl', [$this->url, 'getClassLikeUrl']);
		$latte->addFunction('classLikeSourceUrl', [$this->url, 'getClassLikeSourceUrl']);
		$latte->addFunction('memberUrl', [$this->url, 'getMemberUrl']);
		$latte->addFunction('memberAnchor', [$this->url, 'getMemberAnchor']);
		$latte->addFunction('memberSourceUrl', [$this->url, 'getMemberSourceUrl']);
		$latte->addFunction('sourceUrl', [$this->url, 'getSourceUrl']);

		return $latte;
	}
}
