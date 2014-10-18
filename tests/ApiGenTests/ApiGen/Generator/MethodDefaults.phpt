<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Generator;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class MethodDefaultsTests extends TestCase
{

	public function testBasicGeneration()
	{
		$this->prepareConfig();

		passthru(APIGEN_BIN . ' generate');
		Assert::true(file_exists(API_DIR . '/index.html'));

		$methodFile = API_DIR . '/class-Project.Method.html';
		Assert::true(file_exists($methodFile));

		Assert::match(
			file_get_contents(__DIR__ . '/MethodDefaults.html'),
			file_get_contents($methodFile)
		);
	}


	private function prepareConfig()
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(PROJECT_DIR);
		$config['destination'] = API_DIR;
		$neonFile->write($config);
	}

}


\run(new MethodDefaultsTests);
