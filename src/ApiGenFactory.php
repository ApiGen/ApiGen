<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Analyzer\NodeVisitors\BodySkipper;
use ApiGenX\Analyzer\NodeVisitors\PhpDocResolver;
use ApiGenX\Renderer\Latte\LatteRendererFactory;
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
	public function create(SymfonyStyle $output, string $projectDir, string $baseDir, string $baseUrl, int $workerCount): ApiGen
	{
		$analyzer = $this->createAnalyzer($output, $projectDir);
		$indexer = $this->createIndexer();
		$renderer = $this->createRenderer($baseDir, $baseUrl, $workerCount);

		return new ApiGen($analyzer, $indexer, $renderer);
	}


	private function createAnalyzer(SymfonyStyle $output, string $projectDir): Analyzer
	{
		$locator = Locator::create($output, $projectDir);
		$phpParserFactory = new ParserFactory();
		$phpParser = $phpParserFactory->create(ParserFactory::PREFER_PHP7);
		$phpNodeTraverser = $this->createPhpNodeTraverser();

		return new Analyzer($locator, $phpParser, $phpNodeTraverser);
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


	private function createIndexer(): Indexer
	{
		return new Indexer();
	}


	private function createRenderer(string $baseDir, string $baseUrl, int $workerCount): Renderer
	{
		return (new LatteRendererFactory)->create($baseDir, $baseUrl, $workerCount);
	}
}
