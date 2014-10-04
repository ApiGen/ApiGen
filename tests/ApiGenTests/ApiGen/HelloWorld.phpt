<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';


class HelloWorldTest extends TestCase
{

	public function testBasicGeneration()
	{
		$this->prepareConfig();

		passthru(APIGEN_BIN . ' generate');
		Assert::true(file_exists(API_DIR . '/index.html'));

		$fooClassFile = API_DIR . '/source-class-ApiGenTests.ApiGen.Project.Foo.html';
		Assert::true(file_exists($fooClassFile));

		$fooClassFileSource = file_get_contents($fooClassFile);
		Assert::true(strlen($fooClassFileSource) > 1);
	}


	/**
	 * Self apigen itself
	 */
	private function prepareConfig()
	{
		$neonFile = new NeonFile(__DIR__ . DS . 'apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(__DIR__ . DS . '../../../src', __DIR__ . DS . '../../../vendor');
		$config['destination'] = API_DIR;
		$neonFile->write($config);
	}

}


\run(new HelloWorldTest);
