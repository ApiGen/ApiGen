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


	public function testCli()
	{
		exec('php ../../../../apigen --source=../Project --base-url=' . self::BASE_URL . ' --destination=' . TEMP_DIR);
		Assert::true(file_exists(TEMP_DIR . '/robots.txt'));

		$robotsTxt = file_get_contents(TEMP_DIR . '/robots.txt');
		Assert::true(Strings::contains($robotsTxt, self::BASE_URL));
	}


	public function testConfig()
	{
		exec('php ../../../../apigen --source=../Project --config=../config/baseUrl.neon --destination=' . TEMP_DIR, $output);
		Assert::true(file_exists(TEMP_DIR . '/robots.txt'));

		$robotsTxt = file_get_contents(TEMP_DIR . '/robots.txt');
		Assert::true(Strings::contains($robotsTxt, self::BASE_URL));
	}

}


\run(new BaseUrlTest);
