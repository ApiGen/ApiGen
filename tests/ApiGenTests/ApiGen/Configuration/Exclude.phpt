<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class ExcludeTest extends TestCase
{

	public function testFileMask()
	{
		$this->prepareConfig(array('Package*'));
		passthru(APIGEN_BIN . ' generate');
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.PackageA.html'));
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.PackageB.html'));

		$this->prepareConfig(array('*QueueFactory*'));
		passthru(APIGEN_BIN . ' generate');
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.QueueFactory.html'));

		$this->prepareConfig(array('*/Category*'));
		passthru(APIGEN_BIN . ' generate');
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.Entities.Category.html'));
	}


	public function fileMaskWithSuffix()
	{
		$this->prepareConfig(array('*/QueueFactory.php'));
		passthru(APIGEN_BIN . ' generate');
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.QueueFactory.html'));
	}


	public function textDirExclude()
	{
		$this->prepareConfig(array('Entities'));
		passthru(APIGEN_BIN . ' generate');
		Assert::false(file_exists(API_DIR . '/namespace-ProjectBeta.Entities.html'));
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.Entities.Category.html'));
	}


	public function testNoneFound()
	{
		$this->prepareConfig(array('*'));
		passthru(APIGEN_BIN . ' generate', $output);
		Assert::same(0, $output);
	}


	private function prepareConfig(array $exclude = array())
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(PROJECT_DIR, PROJECT_BETA_DIR);
		$config['destination'] = API_DIR;
		if ($exclude) {
			$config['exclude'] = $exclude;
		}
		$neonFile->write($config);
	}

}


\run(new ExcludeTest);
