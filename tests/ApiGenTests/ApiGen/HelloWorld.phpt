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
