<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class MainTest extends TestCase
{

	const TITLE = 'Project API';


	public function testConfig()
	{
		$this->prepareConfig();
		passthru(APIGEN_BIN . ' generate');

		Assert::match(
			file_get_contents(__DIR__ . '/Main.html'),
			file_get_contents(API_DIR . '/index.html')
		);
	}


	private function prepareConfig()
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(PROJECT_DIR, dirname(PROJECT_DIR) . '/ProjectBeta');
		$config['destination'] = API_DIR;
		$config['main'] = 'ProjectBeta';
		$neonFile->write($config);
	}

}


\run(new MainTest);
