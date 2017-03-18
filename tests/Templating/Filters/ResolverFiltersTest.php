<?php declare(strict_types=1);

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


    protected function setUp(): void
    {
        $elementResolverMock = $this->createMock(ElementResolver::class);
        $elementResolverMock->method('getClass')->willReturnUsing(function ($arg) {
            return ($arg === 'SomeClass') ? 'ResolvedClass' : null;
        });
        $elementResolverMock->method('getClass')->twice()->willReturnNull();
        $elementResolverMock->method('resolveElement')->willReturnUsing(function ($arg) {
            return ($arg === 'SomeElement') ? 'ResolvedElement' : null;
        });
        $this->resolverFilters = new ResolverFilters($elementResolverMock);
    }


    public function testGetClass(): void
    {
        $this->assertSame('ResolvedClass', $this->resolverFilters->getClass('SomeClass'));
    }


    public function testGetClassForNonExistingClass(): void
    {
        $this->assertFalse($this->resolverFilters->getClass('NotExistingClass'));
    }


    public function testResolveElement(): void
    {
        $reflectionElementMock = $this->createMock(ReflectionElement::class);
        $this->assertSame(
            'ResolvedElement',
            $this->resolverFilters->resolveElement('SomeElement', $reflectionElementMock)
        );
    }


    public function testResolveElementForNonExistingElement(): void
    {
        $reflectionElementMock = $this->createMock(ReflectionElement::class);
        $this->assertFalse($this->resolverFilters->resolveElement('NonExistingElement', $reflectionElementMock));
    }
}
