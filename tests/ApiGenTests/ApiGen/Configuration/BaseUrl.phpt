<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGen\Neon\NeonFile;
use Tester\Assert;
use Tester\Helpers;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


class BaseUrlTest extends TestCase
{

	const BASE_URL = 'http://nette.org';


	protected function setUp()
	{
		file_put_contents(__DIR__ . '/apigen.neon', '');
	}


	public function testConfig()
	{
		$this->prepareConfig();
		passthru(APIGEN_BIN . ' generate');

		Assert::true(file_exists(API_DIR . '/index.html'));
		Assert::true(file_exists(API_DIR . '/robots.txt'));
		Assert::match(
			'%A%Sitemap: ' . self::BASE_URL . '%A%',
			file_get_contents(API_DIR . '/robots.txt'
		));
	}


	private function prepareConfig()
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(PROJECT_DIR);
		$config['destination'] = API_DIR;
		$config['baseUrl'] = self::BASE_URL;
		$neonFile->write($config);
	}


	protected function tearDown()
	{
		@unlink(__DIR__ . '/apigen.neon');
	}

}


\run(new BaseUrlTest);
