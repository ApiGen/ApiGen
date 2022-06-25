<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte;

use ApiGenX\Renderer\UrlGenerator;
use Latte;
use Throwable;

use function array_filter;


final class LatteEngineFactory
{
	public function __construct(
		private LatteFunctions $functions,
		private UrlGenerator $url,
		private ?string $tempDir,
		private ?string $templatesDir,
	) {
	}


	public function create(): Latte\Engine
	{
		$latte = new Latte\Engine();
		$latte->setStrictTypes();
		$latte->setExceptionHandler(fn(Throwable $e) => throw $e);
		$latte->setTempDirectory($this->tempDir);

		$loader = new LatteCascadingLoader(array_filter([$this->templatesDir, __DIR__ . '/Template']));
		$latte->setLoader($loader);

		$latte->addFunction('isClass', [$this->functions, 'isClass']);
		$latte->addFunction('isInterface', [$this->functions, 'isInterface']);
		$latte->addFunction('isTrait', [$this->functions, 'isTrait']);
		$latte->addFunction('isEnum', [$this->functions, 'isEnum']);

		$latte->addFunction('textWidth', [$this->functions, 'textWidth']);
		$latte->addFunction('htmlWidth', [$this->functions, 'htmlWidth']);
		$latte->addFunction('highlight', [$this->functions, 'highlight']);
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
