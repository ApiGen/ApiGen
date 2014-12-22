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
		$this->assertEquals(3, count($files));

		$files = $this->scanner->scan(__DIR__ . '/Source', ['*Another*']);
		$this->assertEquals(2, count($files));

		$files = $this->scanner->scan(__DIR__ . '/Source', [], ['php5']);
		$this->assertEquals(1, count($files));
	}


	public function testGetSymlinks()
	{
		$this->scanner->scan(__DIR__ . '/Source');
		$this->assertCount(0, $this->scanner->getSymlinks());
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
