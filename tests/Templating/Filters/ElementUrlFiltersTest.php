<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Reflection\ReflectionElement;
use ApiGen\Templating\Filters\ElementUrlFilters;
use Mockery;
use PHPUnit_Framework_TestCase;


class ElementUrlFiltersTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ElementUrlFilters
	 */
	private $elementUrlFilters;


	protected function setUp()
	{
		$this->elementUrlFilters = new ElementUrlFilters($this->getElementUrlFactoryMock());
	}


	public function testElementUrl()
	{
		$reflectionElementMock = Mockery::mock('ApiGen\Reflection\ReflectionElement');
		$reflectionElementMock->shouldReceive('getName')->andReturn('ReflectionElement');
		$this->assertSame('url-for-ReflectionElement', $this->elementUrlFilters->elementUrl($reflectionElementMock));
	}


	public function testClassUrl()
	{
		$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$reflectionClassMock->shouldReceive('getName')->andReturn('ReflectionClass');
		$this->assertSame('url-for-ReflectionClass', $this->elementUrlFilters->classUrl($reflectionClassMock));
	}


	public function testMethodUrl()
	{
		$reflectionMethodMock = Mockery::mock('ApiGen\Reflection\ReflectionMethod');
		$reflectionMethodMock->shouldReceive('getName')->andReturn('ReflectionMethod');
		$this->assertSame('url-for-ReflectionMethod', $this->elementUrlFilters->methodUrl($reflectionMethodMock));
	}


	public function testPropertyUrl()
	{
		$reflectionPropertyMock = Mockery::mock('ApiGen\Reflection\ReflectionProperty');
		$reflectionPropertyMock->shouldReceive('getName')->andReturn('ReflectionProperty');
		$this->assertSame('url-for-ReflectionProperty', $this->elementUrlFilters->propertyUrl($reflectionPropertyMock));
	}


	public function testConstantUrl()
	{
		$reflectionConstantMock = Mockery::mock('ApiGen\Reflection\ReflectionConstant');
		$reflectionConstantMock->shouldReceive('getName')->andReturn('ReflectionConstant');
		$this->assertSame('url-for-ReflectionConstant', $this->elementUrlFilters->constantUrl($reflectionConstantMock));
	}


	public function testFunctionUrl()
	{
		$reflectionFunctionMock = Mockery::mock('ApiGen\Reflection\ReflectionFunction');
		$reflectionFunctionMock->shouldReceive('getName')->andReturn('ReflectionFunction');
		$this->assertSame('url-for-ReflectionFunction', $this->elementUrlFilters->functionUrl($reflectionFunctionMock));
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getElementUrlFactoryMock()
	{
		$elementUrlFactoryMock = Mockery::mock('ApiGen\Templating\Filters\Helpers\ElementUrlFactory');
		$elementUrlFactoryMock->shouldReceive('createForElement')->andReturnUsing(function (ReflectionElement $arg) {
			return 'url-for-' . $arg->getName();
		});
		$elementUrlFactoryMock->shouldReceive('createForClass')->andReturnUsing(function (ReflectionElement $arg) {
			return 'url-for-' . $arg->getName();
		});
		$elementUrlFactoryMock->shouldReceive('createForMethod')->andReturnUsing(function (ReflectionElement $arg) {
			return 'url-for-' . $arg->getName();
		});
		$elementUrlFactoryMock->shouldReceive('createForProperty')->andReturnUsing(function (ReflectionElement $arg) {
			return 'url-for-' . $arg->getName();
		});
		$elementUrlFactoryMock->shouldReceive('createForConstant')->andReturnUsing(function (ReflectionElement $arg) {
			return 'url-for-' . $arg->getName();
		});
		$elementUrlFactoryMock->shouldReceive('createForFunction')->andReturnUsing(function (ReflectionElement $arg) {
			return 'url-for-' . $arg->getName();
		});
		return $elementUrlFactoryMock;
	}

}
