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

		Assert::contains(
			'<li class="main"><a href="namespace-ProjectBeta.html">ProjectBeta',
			$this->getFileContentInOneLine(API_DIR . '/index.html')
		);
	}


	private function prepareConfig()
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(PROJECT_DIR, PROJECT_BETA_DIR);
		$config['destination'] = API_DIR;
		$config['main'] = 'ProjectBeta';
		$neonFile->write($config);
	}

}


\run(new MainTest);
