<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class MainTest extends TestCase
{

	const TITLE = 'Project API';


	public function testConfig()
	{
		$this->runGenerateCommand('--main=ProjectBeta', PROJECT_DIR . ' -s ' . PROJECT_BETA_DIR);
		Assert::contains(
			'<li class="active main"><a href="namespace-ProjectBeta.html">ProjectBeta',
			$this->getFileContentInOneLine(API_DIR . '/index.html')
		);
	}

}


(new MainTest)->run();
