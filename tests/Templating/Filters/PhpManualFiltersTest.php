<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Templating\Filters\PhpManualFilters;
use Mockery;
use PHPUnit_Framework_TestCase;


class PhpManualFiltersTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var PhpManualFilters
	 */
	private $phpManualFilters;


	protected function setUp()
	{
		$this->phpManualFilters = new PhpManualFilters;
	}


	public function testManualUrlForExtension()
	{
		$reflectionExtension = Mockery::mock('ApiGen\Reflection\ReflectionExtension');
		$reflectionExtension->shouldReceive('getName')->andReturn('pdf');

		$this->assertSame(
			'http://php.net/manual/en/book.pdf.php',
			$this->phpManualFilters->manualUrl($reflectionExtension)
		);
	}


	public function testManualUrlForReservedClass()
	{
		$reflectionClass = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$reflectionClass->shouldReceive('getName')->andReturn('stdClass');

		$this->assertSame(
			'http://php.net/manual/en/reserved.classes.php',
			$this->phpManualFilters->manualUrl($reflectionClass)
		);
	}


	public function testManualUrlForClass()
	{
		$reflectionExtension = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$reflectionExtension->shouldReceive('getName')->andReturn('splFileInfo');

		$this->assertSame(
			'http://php.net/manual/en/class.splfileinfo.php',
			$this->phpManualFilters->manualUrl($reflectionExtension)
		);
	}


	public function testManualUrlForProperty()
	{
		$reflectionClass = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$reflectionClass->shouldReceive('getName')->andReturn('ZipArchive');

		$reflectionProperty = Mockery::mock('ApiGen\Reflection\ReflectionProperty');
		$reflectionProperty->shouldReceive('getName')->andReturn('status');
		$reflectionProperty->shouldReceive('getDeclaringClass')->andReturn($reflectionClass);

		$this->assertSame(
			'http://php.net/manual/en/class.ziparchive.php#ziparchive.props.status',
			$this->phpManualFilters->manualUrl($reflectionProperty)
		);
	}


	public function testManualUrlForMethod()
	{
		$reflectionClass = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$reflectionClass->shouldReceive('getName')->andReturn('splFileInfo');

		$reflectionMethod = Mockery::mock('ApiGen\Reflection\ReflectionMethod');
		$reflectionMethod->shouldReceive('getName')->andReturn('isLink');
		$reflectionMethod->shouldReceive('getDeclaringClass')->andReturn($reflectionClass);

		$this->assertSame(
			'http://php.net/manual/en/splfileinfo.islink.php',
			$this->phpManualFilters->manualUrl($reflectionMethod)
		);
	}


	public function testManualUrlForFunction()
	{
		$reflectionClass = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$reflectionClass->shouldReceive('getName')->andReturn('');

		$reflectionFunction = Mockery::mock('ApiGen\Reflection\ReflectionFunction');
		$reflectionFunction->shouldReceive('getName')->andReturn('json-decode');
		$reflectionFunction->shouldReceive('getDeclaringClass')->andReturn($reflectionClass);

		$this->assertSame(
			'http://php.net/manual/en/function.json-decode.php',
			$this->phpManualFilters->manualUrl($reflectionFunction)
		);
	}

}
