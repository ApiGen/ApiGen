<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Templating\Filters\ResolverFilters;
use Mockery;
use PHPUnit\Framework\TestCase;

class ResolverFiltersTest extends TestCase
{

    /**
     * @var ResolverFilters
     */
    private $resolverFilters;


    protected function setUp()
    {
        $elementResolverMock = Mockery::mock(ElementResolver::class);
        $elementResolverMock->shouldReceive('getClass')->andReturnUsing(function ($arg) {
            return ($arg === 'SomeClass') ? 'ResolvedClass' : null;
        });
        $elementResolverMock->shouldReceive('getClass')->twice()->andReturnNull();
        $elementResolverMock->shouldReceive('resolveElement')->andReturnUsing(function ($arg) {
            return ($arg === 'SomeElement') ? 'ResolvedElement' : null;
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
        $reflectionElementMock = Mockery::mock(ReflectionElement::class);
        $this->assertSame(
            'ResolvedElement',
            $this->resolverFilters->resolveElement('SomeElement', $reflectionElementMock)
        );
    }


    public function testResolveElementForNonExistingElement()
    {
        $reflectionElementMock = Mockery::mock(ReflectionElement::class);
        $this->assertFalse($this->resolverFilters->resolveElement('NonExistingElement', $reflectionElementMock));
    }
}
