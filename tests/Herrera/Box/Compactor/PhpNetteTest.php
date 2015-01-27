<?php

namespace ApiGen\Tests\Herrera\Box\Compactor;

use ApiGen\Herrera\Box\Compactor\PhpNette;
use PHPUnit_Framework_TestCase;


class PhpNetteTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var PhpNette
	 */
	private $phpNetteCompactor;


	protected function setUp()
	{
		$this->phpNetteCompactor = new PhpNette;
	}


	public function testCompact()
	{
		$this->assertSame(
			file_get_contents(__DIR__ . '/Source/expected.php'),
			$this->phpNetteCompactor->compact(file_get_contents(__DIR__ . '/Source/source.php'))
		);
	}


	public function testSupports()
	{
		$this->assertTrue($this->phpNetteCompactor->supports('file.php'));
		$this->assertFalse($this->phpNetteCompactor->supports('file.json'));
	}

}
