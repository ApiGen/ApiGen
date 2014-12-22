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
		$configurationMock->shouldReceive('getOption')->with('template')->andReturn(['templates' => ['source' => ['filename' => 'source-file-%s.html']]]);
		$this->sourceFilters = new SourceFilters($configurationMock);
	}


	public function testStaticFile()
	{
		file_put_contents(TEMP_DIR . '/some-file.txt', '...');
		$link = $this->sourceFilters->staticFile('some-file.txt');
		$this->assertSame('some-file.txt?6eae3a5b062c6d0d79f070c26e6d62486b40cb46', $link);
	}


	public function testSourceUrlFunction()
	{
		$this->assertSame(
			'source-file-function-someFunction.html#15-25',
			$this->sourceFilters->sourceUrl($this->getReflectionFunction())
		);
	}


	public function testSourceUrlClass()
	{
		$this->assertSame(
			'source-file-class-someClass.html#10-100',
			$this->sourceFilters->sourceUrl($this->getReflectionClass())
		);
	}


	public function testSourceUrlConstant()
	{
		$this->assertSame(
			'source-file-class-someClass.html#20',
			$this->sourceFilters->sourceUrl($this->getReflectionConstant())
		);
	}


	public function testSourceUrlConstantWithoutClass()
	{
		$this->assertSame(
			'source-file-constant-someConstant.html#80',
			$this->sourceFilters->sourceUrl($this->getReflectionConstantWithoutClass())
		);
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionFunction()
	{
		$reflectionFunction = Mockery::mock('ApiGen\Reflection\ReflectionFunction');
		$reflectionFunction->shouldReceive('getName')->andReturn('someFunction');
		$reflectionFunction->shouldReceive('getStartLine')->andReturn(15);
		$reflectionFunction->shouldReceive('getEndLine')->andReturn(25);
		return $reflectionFunction;
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionClass()
	{
		$reflectionClass = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$reflectionClass->shouldReceive('getName')->andReturn('someClass');
		$reflectionClass->shouldReceive('getStartLine')->andReturn(10);
		$reflectionClass->shouldReceive('getEndLine')->andReturn(100);
		return $reflectionClass;
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionConstant()
	{
		$reflectionConstant = Mockery::mock('ApiGen\Reflection\ReflectionConstant');
		$reflectionConstant->shouldReceive('getName')->andReturn('someConstant');
		$reflectionConstant->shouldReceive('getDeclaringClassName')->andReturn('someClass');
		$reflectionConstant->shouldReceive('getStartLine')->andReturn(20);
		$reflectionConstant->shouldReceive('getEndLine')->andReturn(20);
		return $reflectionConstant;
	}


	private function getReflectionConstantWithoutClass()
	{
		$reflectionConstant = Mockery::mock('ApiGen\Reflection\ReflectionConstant');
		$reflectionConstant->shouldReceive('getName')->andReturn('someConstant');
		$reflectionConstant->shouldReceive('getDeclaringClassName')->andReturn(NULL);
		$reflectionConstant->shouldReceive('getStartLine')->andReturn(80);
		$reflectionConstant->shouldReceive('getEndLine')->andReturn(80);
		return $reflectionConstant;
	}

}
