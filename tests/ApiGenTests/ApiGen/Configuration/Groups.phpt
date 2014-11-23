<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class GroupsTest extends TestCase
{

	public function testDefault()
	{
		$this->prepareConfig();
		passthru(APIGEN_BIN . ' generate');

		$indexContent = $this->getFileContentInOneLine(API_DIR . '/index.html');
		Assert::match(
			'%A%<a href="namespace-ProjectBeta.html">ProjectBeta</a>%A%',
			$indexContent
		);
	}


	public function testNone()
	{
		$this->prepareConfig('none');
		passthru(APIGEN_BIN . ' generate');

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
		$this->prepareConfig('packages');
		passthru(APIGEN_BIN . ' generate');

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
		$this->prepareConfig('namespaces');
		passthru(APIGEN_BIN . ' generate');

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


	/**
	 * @param string $group
	 */
	private function prepareConfig($group = NULL)
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = [PROJECT_BETA_DIR];
		$config['destination'] = API_DIR;
		if ($group !== NULL) {
			$config['groups'] = $group;
		}
		$neonFile->write($config);
	}

}


(new GroupsTest)->run();
