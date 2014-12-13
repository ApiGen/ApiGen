<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class TreeTest extends TestCase
{

	public function testConfig()
	{
		$this->runGenerateCommand('--tree');
		Assert::true(file_exists(API_DIR . '/tree.html'));
	}

}


(new TreeTest)->run();
