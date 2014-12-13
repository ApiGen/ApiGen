<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class DownloadTest extends TestCase
{

	public function testConfig()
	{
		$this->runGenerateCommand('--download');

		Assert::true(file_exists(API_DIR . '/index.html'));
		Assert::true(file_exists(API_DIR . '/API-documentation.zip'));
	}

}


(new DownloadTest)->run();
