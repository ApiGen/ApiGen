<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Console;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class IOTest extends TestCase
{

	public function testNotQuiet()
	{
		$output = $this->runGenerateCommand();
		Assert::notSame([], $output);
	}


	public function testQuiet()
	{
		$output = $this->runGenerateCommand('-q');
		Assert::same([], $output);
	}

}


(new IOTest)->run();
