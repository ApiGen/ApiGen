<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen;

use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


class HelloWorldTest extends TestCase
{

	public function testBasicGeneration()
	{
		$configPath = __DIR__ . '/config/apigen.neon';
		Assert::true(file_exists($configPath));

		$config = atomicConfig($configPath);
		Assert::true(file_exists($configPath));

		passthru(APIGEN_BIN . " --config=$config");
		Assert::true(file_exists(API_DIR . '/index.html'));

		$fooClassFile = API_DIR . '/source-class-ApiGenTests.ApiGen.Project.Foo.html';
		Assert::true(file_exists($fooClassFile));

		$fooClassFileSource = file_get_contents($fooClassFile);
		Assert::true(strlen($fooClassFileSource) > 1);
	}

}


\run(new HelloWorldTest);
