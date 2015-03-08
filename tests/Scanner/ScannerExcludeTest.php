<?php

namespace ApiGen\Tests\Scanner;

use ApiGen\Scanner\Scanner;
use PHPUnit_Framework_TestCase;


class ScannerExcludeTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var Scanner
	 */
	private $scanner;


	protected function setUp()
	{
		$this->scanner = new Scanner;
	}


	/**
	 * Issue #412
	 */
	public function testExcludeAppliedOnlyOnSourcesPath()
	{
		$files = $this->scanner->scan(__DIR__ . '/Source', ['tests']);
		$this->assertCount(3, $files);
	}


	/**
	 * Issue #529
	 */
	public function testExcludeDirRelativeToSource()
	{
		$source = __DIR__ . '/ScannerExcludeSource/src';
		$this->assertCount(0, $this->scanner->scan($source, ['Core/smarty_cache']));
		$this->assertCount(0, $this->scanner->scan($source, ['/Core/smarty_cache']));
		$this->assertCount(1, $this->scanner->scan($source, ['src/Core/smarty_cache']));
	}


	public function testExcludeFile()
	{
		$source = __DIR__ . '/ScannerExcludeSource/src';
		$this->assertCount(0, $this->scanner->scan($source, ['ShouldBeExcluded.php']));
		$this->assertCount(0, $this->scanner->scan($source, ['*ShouldBeExcluded*']));
	}

}
