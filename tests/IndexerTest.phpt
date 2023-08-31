<?php declare(strict_types = 1);

namespace ApiGenTests;

use ApiGen\Index\Index;
use ApiGen\Indexer;
use ApiGen\Info\ClassInfo;
use ApiGen\Info\ClassLikeReferenceInfo;
use ApiGen\Info\NameInfo;
use RuntimeException;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;


require __DIR__ . '/../vendor/autoload.php';


/**
 * @testCase
 */
class IndexerTest extends TestCase
{
	public function testPostProcessWithEmptyIndex(): void
	{
		$index = new Index();
		$indexer = new Indexer();

		Assert::noError(fn() => $indexer->postProcess($index));
	}


	public function testPostProcessWithDuplicateEdge(): void
	{
		$baseClass = new ClassInfo(new NameInfo('Foo\\BaseClass'), primary: true);
		$baseClass->abstract = true;

		$childClass = new ClassInfo(new NameInfo('Foo\\ChildClass'), primary: true);
		$childClass->extends = new ClassLikeReferenceInfo('Foo\\BaseClass');
		$childClass->implements['foo\\baseclass'] = new ClassLikeReferenceInfo('Foo\\BaseClass');

		$index = new Index();
		$indexer = new Indexer();
		$indexer->indexFile($index, file: null, primary: true);
		$indexer->indexNamespace($index, 'Foo', 'foo', primary: true, deprecated: false);
		$indexer->indexClassLike($index, $baseClass);
		$indexer->indexClassLike($index, $childClass);

		Assert::exception(
			fn() => $indexer->postProcess($index),
			RuntimeException::class,
			"Invalid directed acyclic graph because it contains duplicate edge (used both as 'class extends' and 'class implements'):\nFoo\\ChildClass -> Foo\\BaseClass",
		);
	}
}


Environment::setup();
(new IndexerTest)->run();
