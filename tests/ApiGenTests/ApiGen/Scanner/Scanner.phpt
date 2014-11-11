<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Scanner;

use ApiGen\PharCompiler;
use ApiGen\Scanner\Scanner;
use Tester\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class ScannerTest extends TestCase
{

	/**
	 * @var Scanner
	 */
	private $scanner;


	protected function setUp()
	{
		$this->scanner = new Scanner;
	}


	public function testType()
	{
		Assert::type(
			'ApiGen\Scanner\Scanner',
			$this->scanner
		);
	}


	public function testScanFiles()
	{
		$files = $this->scanner->scan(array(PROJECT_DIR));
		Assert::equal(14, count($files));

		$files = $this->scanner->scan(array(PROJECT_DIR), array('*Annotation*'));
		Assert::equal(13, count($files));

		$files = $this->scanner->scan(array(PROJECT_DIR), array(), array('php5'));
		Assert::equal(1, count($files));
	}


	public function testSymlinks()
	{
		$this->scanner->scan(array(PROJECT_DIR));
		Assert::count(14, $this->scanner->getSymlinks());
	}


	/**
	 * @throws \RuntimeException
	 */
	public function testNoFound()
	{
		$this->scanner->scan(array(PROJECT_DIR), array(), array('php6'));
	}


	public function testPhar()
	{
		$compiler = new PharCompiler(__DIR__ . '/../../../..');
		$compiler->compile(TEMP_DIR . '/apigen.phar');
		Assert::true(file_exists(TEMP_DIR . '/apigen.phar'));

		$files = $this->scanner->scan(array(TEMP_DIR . '/apigen.phar'));
		Assert::true(count($files) > 400);
	}


	/**
	 * Issue #412
	 */
	public function testExcludeAppliedOnlyOnSourcesPath()
	{
		$files = $this->scanner->scan(array(PROJECT_DIR), array('tests', '*tests*', '*/tests/*'));
		Assert::same(14, count($files));
	}

}


\run(new ScannerTest);
