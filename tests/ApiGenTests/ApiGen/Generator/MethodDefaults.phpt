<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Generator;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class MethodDefaultsTests extends TestCase
{

	public function testBasicGeneration()
	{
		$this->runGenerateCommand();
		Assert::true(file_exists(API_DIR . '/index.html'));

		$methodFile = API_DIR . '/class-Project.Method.html';
		Assert::true(file_exists($methodFile));

		Assert::match(
			file_get_contents(__DIR__ . '/MethodDefaults.html'),
			file_get_contents($methodFile)
		);
	}

}


(new MethodDefaultsTests)->run();
