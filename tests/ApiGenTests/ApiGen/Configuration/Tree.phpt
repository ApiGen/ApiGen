<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class TreeTest extends TestCase
{

	public function testConfig()
	{
		$this->prepareConfig();
		passthru(APIGEN_BIN . ' generate');
		Assert::true(file_exists(API_DIR . '/tree.html'));

		$this->prepareConfig(FALSE);
		passthru(APIGEN_BIN . ' generate');
		Assert::false(file_exists(API_DIR . '/tree.html'));

		$this->prepareConfig(TRUE);
		passthru(APIGEN_BIN . ' generate');
		Assert::true(file_exists(API_DIR . '/tree.html'));
	}


	/**
	 * @param bool $treeState
	 */
	private function prepareConfig($treeState = NULL)
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = [PROJECT_DIR];
		$config['destination'] = API_DIR;
		if ($treeState !== NULL) {
			$config['tree'] = $treeState;
		}
		$neonFile->write($config);
	}

}


(new TreeTest)->run();
