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


class DownloadTest extends TestCase
{

	protected function setUp()
	{
		file_put_contents(__DIR__ . '/apigen.neon', '');
	}


	public function testConfig()
	{
		$this->prepareConfig();
		passthru(APIGEN_BIN . ' generate');

		Assert::true(file_exists(API_DIR . '/index.html'));
		Assert::true(file_exists(API_DIR . '/api-documentation.zip'));
	}


	private function prepareConfig()
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(PROJECT_DIR);
		$config['destination'] = API_DIR;
		$config['download'] = TRUE;
		$neonFile->write($config);
	}


	protected function tearDown()
	{
		@unlink(__DIR__ . '/apigen.neon');
	}

}


\run(new DownloadTest);
