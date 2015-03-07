<?php

namespace ApiGen\Tests\Scanner;

use ApiGen\Scanner\Scanner;
use ApiGen\Tests\ContainerAwareTestCase;


class ScannerTest extends ContainerAwareTestCase
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
		$this->assertCount(3, $files);

		$files = $this->scanner->scan(__DIR__ . '/Source', ['*Another*']);
		$this->assertCount(2, $files);

		$files = $this->scanner->scan(__DIR__ . '/Source', [], ['php5']);
		$this->assertCount(1, $files);
	}


	public function testScanSingleFile()
	{
		$files = $this->scanner->scan(__DIR__ . '/Source/SomeClass.php');
		$this->assertCount(1, $files);
	}


	/**
	 * @expectedException \RuntimeException
	 */
	public function testNoFound()
	{
		$this->scanner->scan(__DIR__ . '/Source', [], ['php6']);
	}


	/**
	 * Issue #412
	 */
	public function testExcludeAppliedOnlyOnSourcesPath()
	{
		$files = $this->scanner->scan(__DIR__ . '/Source', ['tests']);
		$this->assertEquals(3, count($files));
	}

}
