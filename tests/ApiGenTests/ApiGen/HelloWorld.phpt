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
		exec('php ../../../apigen --config=config/apigen.neon');
		Assert::true(file_exists(API_DIR . '/index.html'));

		$fooClassFile = API_DIR . '/source-class-ApiGenTests.ApiGen.Project.Foo.html';
		Assert::true(file_exists($fooClassFile));

		$fooClassFileSource = file_get_contents($fooClassFile);
		Assert::true(strlen($fooClassFileSource) > 1);
	}

}


\run(new HelloWorldTest);
