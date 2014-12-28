<?php

namespace ApiGen\Tests\Parser\Elements;

use ApiGen\Parser\Elements\ElementFilter;
use Mockery;
use PHPUnit_Framework_TestCase;


class ElementFilterTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ElementFilter
	 */
	private $elementFilter;


	protected function setUp()
	{
		$this->elementFilter = new ElementFilter;
	}


	public function testFilterForMain()
	{
		$reflectionElement = Mockery::mock('ApiGen\Reflection\ReflectionElement');
		$reflectionElement->shouldReceive('isMain')->andReturn(TRUE);
		$reflectionElement2 = Mockery::mock('ApiGen\Reflection\ReflectionElement');
		$reflectionElement2->shouldReceive('isMain')->andReturn(FALSE);
		$elements = [$reflectionElement, $reflectionElement2];

		$filteredElements = $this->elementFilter->filterForMain($elements);
		$this->assertCount(1, $filteredElements);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionElement', $filteredElements[0]);
	}


	public function testFilterByAnnotation()
	{
		$reflectionElement = Mockery::mock('ApiGen\Reflection\ReflectionElement');
		$reflectionElement->shouldReceive('hasAnnotation')->with('todo')->andReturn(TRUE);
		$reflectionElement->shouldReceive('hasAnnotation')->with('deprecated')->andReturn(FALSE);

		$todoElements = $this->elementFilter->filterByAnnotation([$reflectionElement], 'todo');
		$this->assertCount(1, $todoElements);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionElement', $todoElements[0]);

		$this->assertCount(0, $this->elementFilter->filterByAnnotation([$reflectionElement], 'deprecated'));
	}

}
