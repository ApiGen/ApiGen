<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen;

use ApiGen\Neon\NeonFile;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


class HelloWorldTest extends TestCase
{

	protected function setUp()
	{
		file_put_contents(__DIR__ . '/apigen.neon', '');
	}


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


	private function prepareConfig()
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(__DIR__ . DS . 'Project');
		$config['destination'] = API_DIR;
		$neonFile->write($config);
	}


	protected function tearDown()
	{
		@unlink(__DIR__ . '/apigen.neon');
	}

}


\run(new HelloWorldTest);
