<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Templating\Filters\ResolverFilters;
use Mockery;
use PHPUnit_Framework_TestCase;


class ResolverFiltersTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ResolverFilters
	 */
	private $resolverFilters;


	protected function setUp()
	{
		$elementResolverMock = Mockery::mock('ApiGen\Generator\Resolvers\ElementResolver');
		$elementResolverMock->shouldReceive('getClass')->andReturnUsing(function ($arg) {
			return ($arg === 'SomeClass') ? 'ResolvedClass' : NULL;
		});
		$elementResolverMock->shouldReceive('getClass')->twice()->andReturnNull();
		$elementResolverMock->shouldReceive('resolveElement')->andReturnUsing(function ($arg) {
			return ($arg === 'SomeElement') ? 'ResolvedElement' : NULL;
		});
		$this->resolverFilters = new ResolverFilters($elementResolverMock);
	}


	public function testGetClass()
	{
		$this->assertSame('ResolvedClass', $this->resolverFilters->getClass('SomeClass'));
	}


	public function testGetClassForNonExistingClass()
	{
		$this->assertFalse($this->resolverFilters->getClass('NotExistingClass'));
	}


	public function testResolveElement()
	{
		$reflectionElementMock = Mockery::mock('ApiGen\Reflection\ReflectionElement');
		$this->assertSame(
			'ResolvedElement',
			$this->resolverFilters->resolveElement('SomeElement', $reflectionElementMock)
		);

	}


	public function testResolveElementForNonExistingElement()
	{
		$reflectionElementMock = Mockery::mock('ApiGen\Reflection\ReflectionElement');
		$this->assertFalse($this->resolverFilters->resolveElement('NonExistingElement', $reflectionElementMock));
	}

}
