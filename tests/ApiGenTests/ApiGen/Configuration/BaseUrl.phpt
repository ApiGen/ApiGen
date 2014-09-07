<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use Nette\Utils\Strings;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


class BaseUrlTest extends TestCase
{
	const BASE_URL = 'http://nette.org';


	public function testConfig()
	{
		exec('php ../../../../apigen --config=../config/baseUrl.neon');
		Assert::true(file_exists(API_DIR . '/index.html'));

		Assert::true(file_exists(API_DIR . '/robots.txt'));

		$robotsTxt = file_get_contents(API_DIR . '/robots.txt');
		Assert::true(Strings::contains($robotsTxt, self::BASE_URL));
	}

}


\run(new BaseUrlTest);
