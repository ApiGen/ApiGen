<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class ExcludeTest extends TestCase
{

	public function testFileMask()
	{
		$this->runGenerateCommand('--exclude="Package*"');
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.PackageA.html'));
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.PackageB.html'));

		$this->runGenerateCommand('--exclude="*QueueFactory*"');
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.QueueFactory.html'));

		$this->runGenerateCommand('--exclude="*/Category*"');
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.Entities.Category.html'));
	}


	public function testFileMaskWithSuffix()
	{
		$this->runGenerateCommand('--exclude="*/QueueFactory.php"');
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.QueueFactory.html'));
	}


	public function testDirExclude()
	{
		$this->runGenerateCommand('--exclude="Entities"');
		Assert::false(file_exists(API_DIR . '/namespace-ProjectBeta.Entities.html'));
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.Entities.Category.html'));
	}


	public function testNoneFound()
	{
		$this->runGenerateCommand('--exclude="*"');
		passthru(APIGEN_BIN . ' generate', $output);
		Assert::same(0, $output);
	}

}


(new ExcludeTest)->run();
