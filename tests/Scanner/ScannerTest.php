<?php

namespace ApiGen\Tests\Scanner;

use ApiGen\Scanner\Scanner;
use PHPUnit_Framework_TestCase;


class ScannerTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var Scanner
	 */
	private $scanner;


	protected function setUp()
	{
		$this->scanner = new Scanner;
	}


	public function testScan()
	{
		$files = $this->scanner->scan(__DIR__ . '/Source');
		$this->assertCount(4, $files);

		$files = $this->scanner->scan(__DIR__ . '/Source', ['*Another*']);
		$this->assertCount(3, $files);

		$files = $this->scanner->scan(__DIR__ . '/Source', [], ['php5']);
		$this->assertCount(1, $files);
	}


	public function testScanSingleFile()
	{
		$files = $this->scanner->scan(__DIR__ . '/Source/SomeClass.php');
		$this->assertCount(1, $files);
	}


	public function testNoFound()
	{
		$this->assertCount(0, $this->scanner->scan(__DIR__ . '/Source', [], ['php6']));
	}

}
