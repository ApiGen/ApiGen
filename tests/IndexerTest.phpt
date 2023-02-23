<?php declare(strict_types = 1);

namespace ApiGenTests;

use ApiGen\Index\Index;
use ApiGen\Indexer;
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

		Assert::noError(function () use ($indexer, $index): void {
			$indexer->postProcess($index);
		});
	}
}


Environment::setup();
(new IndexerTest)->run();
