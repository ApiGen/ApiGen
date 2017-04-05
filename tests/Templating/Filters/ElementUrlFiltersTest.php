<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Templating\Filters\ElementUrlFilters;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ElementUrlFiltersTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ElementUrlFilters
     */
    private $elementUrlFilters;

    protected function setUp(): void
    {
        $this->elementUrlFilters = $this->container->getByType(ElementUrlFilters::class);
    }

    public function testClassUrl(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $reflectionClassMock->method('getName')
            ->willReturn('SomeClass');

        $this->assertSame(
            'class-SomeClass.html',
            $this->elementUrlFilters->classUrl($reflectionClassMock)
        );
    }

    public function testMethodUrl(): void
    {
        $reflectionMethodMock = $this->createMock(MethodReflectionInterface::class);
        $reflectionMethodMock->method('getDeclaringClassName')
            ->willReturn('SomeClass');
        $reflectionMethodMock->method('getName')
            ->willReturn('SomeMethod');

        $this->assertSame(
            'class-SomeClass.html#_SomeMethod',
            $this->elementUrlFilters->methodUrl($reflectionMethodMock)
        );
    }

    public function testPropertyUrl(): void
    {
        $reflectionPropertyMock = $this->createMock(PropertyReflectionInterface::class);
        $reflectionPropertyMock->method('getDeclaringClassName')
            ->willReturn('SomeClass');
        $reflectionPropertyMock->method('getName')
            ->willReturn('SomeProperty');

        $this->assertSame(
            'class-SomeClass.html#$SomeProperty',
            $this->elementUrlFilters->propertyUrl($reflectionPropertyMock)
        );
    }

    public function testConstantUrl(): void
    {
        $reflectionConstantMock = $this->createMock(ConstantReflectionInterface::class);
        $reflectionConstantMock->method('getDeclaringClassName')
            ->willReturn('SomeClass');
        $reflectionConstantMock->method('getName')
            ->willReturn('SomeConstant');

        $this->assertSame(
            'class-SomeClass.html#SomeConstant',
            $this->elementUrlFilters->constantUrl($reflectionConstantMock)
        );
    }

    public function testFunctionUrl(): void
    {
        $reflectionFunctionMock = $this->createMock(FunctionReflectionInterface::class);
        $reflectionFunctionMock->method('getName')
            ->willReturn('SomeFunction');

        $this->assertSame('function-SomeFunction.html', $this->elementUrlFilters->functionUrl($reflectionFunctionMock));
    }
}
