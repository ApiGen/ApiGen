<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class GroupsTest extends TestCase
{

	public function testDefault()
	{
		$this->runGenerateCommand(NULL, PROJECT_DIR . ' -s ' . PROJECT_BETA_DIR);
		$indexContent = $this->getFileContentInOneLine(API_DIR . '/index.html');
		Assert::match(
			'%A%<a href="namespace-ProjectBeta.html">ProjectBeta</a>%A%',
			$indexContent
		);
	}


	public function testNone()
	{
		$this->runGenerateCommand('--groups=none', PROJECT_DIR . ' -s ' . PROJECT_BETA_DIR);
		$indexContent = $this->getFileContentInOneLine(API_DIR . '/index.html');
		Assert::notContains(
			'<a href="namespace-ProjectBeta.html">ProjectBeta</a>',
			$indexContent
		);
		Assert::notContains(
			'<li><a href="package-A.html">A</a>',
			$indexContent
		);
	}


	public function testPackages()
	{
		$this->runGenerateCommand('--groups=packages', PROJECT_DIR . ' -s ' . PROJECT_BETA_DIR);
		$indexContent = $this->getFileContentInOneLine(API_DIR . '/index.html');
		Assert::match(
			'%A%<li><a href="package-A.html">A</a>%A%',
			$indexContent
		);
		Assert::match(
			'%A%<li><a href="package-B.html">B</a>%A%',
			$indexContent
		);
	}


	public function testNamespace()
	{
		$this->runGenerateCommand('--groups=namespaces', PROJECT_DIR . ' -s ' . PROJECT_BETA_DIR);
		$indexContent = $this->getFileContentInOneLine(API_DIR . '/index.html');
		Assert::match(
			'%A%<a href="namespace-ProjectBeta.html">ProjectBeta</a>%A%',
			$indexContent
		);
		Assert::notContains(
			'<li><a href="package-A.html">A</a>',
			$indexContent
		);
	}

}


(new GroupsTest)->run();
