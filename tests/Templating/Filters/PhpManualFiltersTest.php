<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionConstant;
use ApiGen\Parser\Reflection\ReflectionExtension;
use ApiGen\Parser\Reflection\ReflectionFunction;
use ApiGen\Parser\Reflection\ReflectionMethod;
use ApiGen\Parser\Reflection\ReflectionProperty;
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
		$reflectionMock = Mockery::mock('TokenReflection\IReflectionExtension', 'Nette\Object');
		$reflectionMock->shouldReceive('getName')->andReturn('pdf');
		$reflectionExtension = new ReflectionExtension($reflectionMock);

		$this->assertSame(
			'http://php.net/manual/en/book.pdf.php',
			$this->phpManualFilters->manualUrl($reflectionExtension)
		);
	}


	public function testManualUrlForDateExtension()
	{
		$reflectionMock = Mockery::mock('TokenReflection\IReflectionExtension', 'Nette\Object');
		$reflectionMock->shouldReceive('getName')->andReturn('date');
		$reflectionExtension = new ReflectionExtension($reflectionMock);

		$this->assertSame(
			'http://php.net/manual/en/book.datetime.php',
			$this->phpManualFilters->manualUrl($reflectionExtension)
		);
	}


	public function testManualUrlForCoreExtension()
	{
		$reflectionExtension = Mockery::mock('ApiGen\Parser\Reflection\ReflectionExtension');
		$reflectionExtension->shouldReceive('getName')->andReturn('core');

		$this->assertSame(
			'http://php.net/manual/en',
			$this->phpManualFilters->manualUrl($reflectionExtension)
		);
	}


	public function testManualUrlForReservedClass()
	{
		$reflectionClass = Mockery::mock('ApiGen\Parser\Reflection\ReflectionClass');
		$reflectionClass->shouldReceive('getName')->andReturn('stdClass');

		$this->assertSame(
			'http://php.net/manual/en/reserved.classes.php',
			$this->phpManualFilters->manualUrl($reflectionClass)
		);
	}


	public function testManualUrlForClass()
	{
		$reflectionMock = Mockery::mock('TokenReflection\IReflectionExtension', 'Nette\Object');
		$reflectionMock->shouldReceive('getName')->andReturn('splFileInfo');
		$reflectionClass = new ReflectionClass($reflectionMock);

		$this->assertSame(
			'http://php.net/manual/en/class.splfileinfo.php',
			$this->phpManualFilters->manualUrl($reflectionClass)
		);
	}


	public function testManualUrlForProperty()
	{
		$reflectionMock = Mockery::mock('TokenReflection\IReflectionExtension', 'Nette\Object');
		$reflectionMock->shouldReceive('getName')->andReturn('ZipArchive');
		$reflectionClass = new ReflectionClass($reflectionMock);

		$parserResultMock = Mockery::mock('ApiGen\Parser\ParserResult');
		$parserResultMock->shouldReceive('getElementsByType')->andReturn(['ZipArchive' => $reflectionClass]);
		$reflectionClass->setParserResult($parserResultMock);

		$reflectionMock = Mockery::mock('TokenReflection\IReflectionExtension', 'Nette\Object');
		$reflectionMock->shouldReceive('getName')->andReturn('status');
		$reflectionMock->shouldReceive('getDeclaringClass')->andReturn($reflectionClass);
		$reflectionMock->shouldReceive('getDeclaringClassName')->andReturn($reflectionClass->getName());

		$reflectionProperty = new ReflectionProperty($reflectionMock);
		$reflectionProperty->setParserResult($parserResultMock);

		$this->assertSame(
			'http://php.net/manual/en/class.ziparchive.php#ziparchive.props.status',
			$this->phpManualFilters->manualUrl($reflectionProperty)
		);
	}


	public function testManualUrlForMethod()
	{
		$reflectionClass = Mockery::mock('ApiGen\Parser\Reflection\ReflectionClass');
		$reflectionClass->shouldReceive('getName')->andReturn('splFileInfo');

		$parserResultMock = Mockery::mock('ApiGen\Parser\ParserResult');
		$parserResultMock->shouldReceive('getElementsByType')->andReturn(['splFileInfo' => $reflectionClass]);

		$reflectionMock = Mockery::mock('TokenReflection\IReflectionMethod', 'Nette\Object');
		$reflectionMock->shouldReceive('getName')->andReturn('isLink');
		$reflectionMock->shouldReceive('getDeclaringClass')->andReturn($reflectionClass);
		$reflectionMock->shouldReceive('getDeclaringClassName')->andReturn('splFileInfo');

		$reflectionMethod = new ReflectionMethod($reflectionMock);
		$reflectionMethod ->setParserResult($parserResultMock);

		$this->assertSame(
			'http://php.net/manual/en/splfileinfo.islink.php',
			$this->phpManualFilters->manualUrl($reflectionMethod)
		);
	}


	public function testManualUrlForFunction()
	{
		$reflectionClass = Mockery::mock('ApiGen\Parser\Reflection\ReflectionClass');
		$reflectionClass->shouldReceive('getName')->andReturn('');

		$parserResultMock = Mockery::mock('ApiGen\Parser\ParserResult');
		$parserResultMock->shouldReceive('getElementsByType')->andReturn(['json-decode' => $reflectionClass]);

		$reflectionMock = Mockery::mock('TokenReflection\IReflectionFunction', 'Nette\Object');
		$reflectionMock->shouldReceive('getName')->andReturn('json-decode');

		$reflectionFunction = new ReflectionFunction($reflectionMock);
		$reflectionFunction->setParserResult($parserResultMock);

		$this->assertSame(
			'http://php.net/manual/en/function.json-decode.php',
			$this->phpManualFilters->manualUrl($reflectionFunction)
		);
	}


	public function testManualUrlForConstant()
	{
		$reflectionClass = Mockery::mock('ApiGen\Parser\Reflection\ReflectionClass');
		$reflectionClass->shouldReceive('getName')->andReturn('ReflectionProperty');

		$parserResultMock = Mockery::mock('ApiGen\Parser\ParserResult');
		$parserResultMock->shouldReceive('getElementsByType')->andReturn(['reflection' => $reflectionClass]);

		$reflectionMock = Mockery::mock('TokenReflection\IReflectionConstant', 'Nette\Object');
		$reflectionMock->shouldReceive('getName')->andReturn('IS_STATIC');
		$reflectionMock->shouldReceive('getDeclaringClassName')->andReturn('reflection');

		$reflectionConstant = new ReflectionConstant($reflectionMock);
		$reflectionConstant->setParserResult($parserResultMock);

		$this->assertSame(
			'http://php.net/manual/en/class.reflectionproperty.php#reflectionproperty.constants.is-static',
			$this->phpManualFilters->manualUrl($reflectionConstant)
		);
	}


	public function testManualUrlForNonExisting()
	{
		$reflectionClass = Mockery::mock('ApiGen\Parser\Reflection\ReflectionClass');
		$reflectionClass->shouldReceive('getName')->andReturn();

		$reflectionMagicProperty = Mockery::mock('ApiGen\Parser\Reflection\ReflectionMagicProperty');
		$reflectionMagicProperty->shouldReceive('getDeclaringClass')->andReturn($reflectionClass);

		$this->assertSame('', $this->phpManualFilters->manualUrl($reflectionMagicProperty));
	}

}
