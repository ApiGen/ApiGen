<?php

namespace ApiGen\Tests\Parser\Elements;

use ApiGen\Parser\Elements\ElementSorter;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Reflection\ReflectionMethod;
use Mockery;
use PHPUnit_Framework_TestCase;


class ElementSorterTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ElementSorter
	 */
	private $elementSorter;


	protected function setUp()
	{
		$this->elementSorter = new ElementSorter;
	}


	public function testSortElementsByFqnConstants()
	{
		$reflectionConstantMock = Mockery::mock(ReflectionConstant::class);
		$reflectionConstantMock->shouldReceive('getDeclaringClassName')->andReturn('B');
		$reflectionConstantMock->shouldReceive('getName')->andReturn('C');

		$reflectionConstantMock2 = Mockery::mock(ReflectionConstant::class);
		$reflectionConstantMock2->shouldReceive('getDeclaringClassName')->andReturn('A');
		$reflectionConstantMock2->shouldReceive('getName')->andReturn('D');

		$elements = [$reflectionConstantMock, $reflectionConstantMock2];

		$sortedElements = $this->elementSorter->sortElementsByFqn($elements);
		$this->assertNotSame($elements, $sortedElements);
		$this->assertSame($reflectionConstantMock2, $sortedElements[0]);
		$this->assertSame($reflectionConstantMock, $sortedElements[1]);
	}


	public function testSortElementsByFqnFunctions()
	{
		$reflectionFunctionMock = Mockery::mock(ReflectionFunction::class);
		$reflectionFunctionMock->shouldReceive('getNamespaceName')->andReturn('B');
		$reflectionFunctionMock->shouldReceive('getName')->andReturn('C');

		$reflectionFunctionMock2 = Mockery::mock(ReflectionFunction::class);
		$reflectionFunctionMock2->shouldReceive('getNamespaceName')->andReturn('A');
		$reflectionFunctionMock2->shouldReceive('getName')->andReturn('D');

		$elements = [$reflectionFunctionMock, $reflectionFunctionMock2];

		$sortedElements = $this->elementSorter->sortElementsByFqn($elements);
		$this->assertNotSame($elements, $sortedElements);
		$this->assertSame($reflectionFunctionMock2, $sortedElements[0]);
		$this->assertSame($reflectionFunctionMock, $sortedElements[1]);
	}


	public function testSortElementsByFqnMethod()
	{
		$reflectionMethodMock = Mockery::mock(ReflectionMethod::class);
		$reflectionMethodMock->shouldReceive('getDeclaringClassName')->andReturn('B');
		$reflectionMethodMock->shouldReceive('getName')->andReturn('C');

		$reflectionMethodMock2 = Mockery::mock(ReflectionMethod::class);
		$reflectionMethodMock2->shouldReceive('getDeclaringClassName')->andReturn('A');
		$reflectionMethodMock2->shouldReceive('getName')->andReturn('D');

		$elements = [$reflectionMethodMock, $reflectionMethodMock2];

		$sortedElements = $this->elementSorter->sortElementsByFqn($elements);
		$this->assertNotSame($elements, $sortedElements);
		$this->assertSame($reflectionMethodMock2, $sortedElements[0]);
		$this->assertSame($reflectionMethodMock, $sortedElements[1]);
	}


	public function testSortElementsByFqnProperties()
	{
		$reflectionMethodMock = Mockery::mock(ReflectionMethod::class);
		$reflectionMethodMock->shouldReceive('getDeclaringClassName')->andReturn('B');
		$reflectionMethodMock->shouldReceive('getName')->andReturn('C');

		$reflectionMethodMock2 = Mockery::mock(ReflectionMethod::class);
		$reflectionMethodMock2->shouldReceive('getDeclaringClassName')->andReturn('A');
		$reflectionMethodMock2->shouldReceive('getName')->andReturn('D');

		$elements = [$reflectionMethodMock, $reflectionMethodMock2];

		$sortedElements = $this->elementSorter->sortElementsByFqn($elements);
		$this->assertNotSame($elements, $sortedElements);
		$this->assertSame($reflectionMethodMock2, $sortedElements[0]);
		$this->assertSame($reflectionMethodMock, $sortedElements[1]);
	}


	public function testSortElementsByFqnNonSupportedType()
	{
		$reflectionClassMock = Mockery::mock(ReflectionClass::class);
		$sortedElements = $this->elementSorter->sortElementsByFqn([$reflectionClassMock]);
		$this->assertSame([$reflectionClassMock], $sortedElements);
	}


	public function testSortElementsByFqnWithEmptyArray()
	{
		$this->assertSame([], $this->elementSorter->sortElementsByFqn([]));
	}

}
