<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class BaseUrlTest extends TestCase
{

	const BASE_URL = 'http://nette.org';


	public function testConfig()
	{
		$this->runGenerateCommand('--baseUrl=' . self::BASE_URL);

		Assert::true(file_exists(API_DIR . '/index.html'));
		Assert::true(file_exists(API_DIR . '/robots.txt'));
		Assert::match(
			'%A%Sitemap: ' . self::BASE_URL . '%A%',
			file_get_contents(API_DIR . '/robots.txt')
		);
	}

}


(new BaseUrlTest)->run();
