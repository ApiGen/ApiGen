<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen;

use Tester\Assert;
use Tester\Helpers;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


class HelloWorldTest extends TestCase
{

	public function testInit()
	{
		Assert::true(class_exists('ApiGen\Generator'));
	}


	public function testBasicGeneration()
	{
		exec('php ../../../apigen --source=../../../src/ApiGen/Generator.php --destination=' . TEMP_DIR, $output);
		Assert::true(file_exists(TEMP_DIR . '/index.html'));
	}

}


\run(new HelloWorldTest);
