<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';


class HelloWorldTest extends TestCase
{

	public function testBasicGeneration()
	{
		$this->runGenerateCommand();
		Assert::true(file_exists(API_DIR . '/index.html'));
	}

}


(new HelloWorldTest)->run();
