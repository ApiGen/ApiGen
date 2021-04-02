<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Analyzer\NodeVisitors\BodySkipper;
use ApiGenX\Analyzer\NodeVisitors\PhpDocResolver;
use ApiGenX\Renderer\LatteEngineFactory;
use ApiGenX\Renderer\LatteFunctions;
use ApiGenX\Renderer\SourceHighlighter;
use ApiGenX\Renderer\UrlGenerator;
use League;
use PhpParser;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TypeParser;
use Symfony\Component\Console\Style\SymfonyStyle;


final class ApiGenFactory
{
	public function create(SymfonyStyle $output, string $sourceDir, string $baseDir, string $baseUrl, int $workerCount): ApiGen
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

		$locator = Locator::create($output, $sourceDir);
		$phpParserFactory = new ParserFactory();
		$phpParser = $phpParserFactory->create(ParserFactory::PREFER_PHP7);
		$phpNodeTraverser = $this->createPhpNodeTraverser();
		$analyzer = new Analyzer($locator, $phpParser, $phpNodeTraverser);

		$indexer = new Indexer();
		$renderer = new Renderer($latte, $urlGenerator, $workerCount);

		return new ApiGen($analyzer, $indexer, $renderer);
	}


	private function createPhpNodeTraverser(): NodeTraverserInterface
	{
		$nameResolver = new NameResolver();
		$nameContext = $nameResolver->getNameContext();

		$phpDocLexer = new Lexer();
		$phpDocParser = new PhpDocParser(new TypeParser(), new ConstExprParser());

		$traverser = new NodeTraverser();
		$traverser->addVisitor(new BodySkipper());
		$traverser->addVisitor($nameResolver);
		$traverser->addVisitor(new PhpDocResolver($phpDocLexer, $phpDocParser, $nameContext));

		return $traverser;
	}
}
