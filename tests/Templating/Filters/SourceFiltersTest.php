<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Templating\Filters\SourceFilters;
use Mockery;
use PHPUnit_Framework_TestCase;


class SourceFiltersTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var SourceFilters
	 */
	private $sourceFilters;


	protected function setUp()
	{
		$configurationMock = Mockery::mock('ApiGen\Configuration\Configuration');
		$configurationMock->shouldReceive('getOption')->with('destination')->andReturn(TEMP_DIR);
		$this->sourceFilters = new SourceFilters($configurationMock);
	}


	public function testStaticFile()
	{
		file_put_contents(TEMP_DIR . '/some-file.txt', '...');
		$link = $this->sourceFilters->staticFile('some-file.txt');
		$this->assertSame('some-file.txt?6eae3a5b062c6d0d79f070c26e6d62486b40cb46', $link);
	}


	public function testSourceAnchorFunction()
	{
		$source = '<span class="php-keyword1">function</span> someFunction';
		$link = $this->sourceFilters->sourceAnchors($source);
		$this->assertSame(
			'<span class="php-keyword1">function</span> <a id="_someFunction" href="#_someFunction">someFunction</a>',
			$link
		);
	}


	public function testSourceAnchorProperty()
	{
		$source = '<span class="php-keyword1">public</span> <span class="php-var">$someProperty</span>;';
		$link = $this->sourceFilters->sourceAnchors($source);
		$this->assertSame(
			'<span class="php-keyword1">public</span> <span class="php-var">'
				. '<a id="$someProperty" href="#$someProperty">$someProperty</a></span>;',
			$link
		);
	}


	public function testSourceAnchorConstant()
	{
		$source = '<span class="php-keyword1">const</span> SOME_CONSTANT = "...";';
		$link = $this->sourceFilters->sourceAnchors($source);
		$this->assertSame(
			'<span class="php-keyword1">const</span> '
				. '<a id="SOME_CONSTANT" href="#SOME_CONSTANT">SOME_CONSTANT</a> = "...";',
			$link
		);
	}


	public function testSourceAnchorClass()
	{
		$source = '<span class="php-keyword1">class</span> SomeClass';
		$link = $this->sourceFilters->sourceAnchors($source);
		$this->assertSame(
			'<span class="php-keyword1">class</span> <a id="SomeClass" href="#SomeClass">SomeClass</a>',
			$link
		);
	}

}
