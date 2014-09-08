<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


class BaseUrlTest extends TestCase
{
	const BASE_URL = 'http://nette.org';


	public function testConfig()
	{
		$config = atomicConfig(__DIR__ . '/../config/baseUrl.neon');
		exec(APIGEN_BIN . " --config=$config");

		Assert::true(file_exists(API_DIR . '/index.html'));
		Assert::true(file_exists(API_DIR . '/robots.txt'));
		Assert::match('%A%Sitemap: ' . self::BASE_URL . '%A%', file_get_contents(API_DIR . '/robots.txt'));
	}

}


\run(new BaseUrlTest);
