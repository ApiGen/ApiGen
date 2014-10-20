<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Scanner;

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
		Assert::count(13, $files);

		$files = $this->scanner->scan(array(PROJECT_DIR), array('*Annotation*'));
		Assert::count(12, $files);

		$files = $this->scanner->scan(array(PROJECT_DIR), array(), array('php5'));
		Assert::count(1, $files);
	}


	public function testSymlinks()
	{
		$this->scanner->scan(array(PROJECT_DIR));
		Assert::count(13, $this->scanner->getSymlinks());
	}

}


\run(new ScannerTest);
