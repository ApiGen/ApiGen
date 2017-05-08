<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassPropertyReflectionInterface;
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
        $reflectionMethodMock = $this->createMock(ClassMethodReflectionInterface::class);
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
        $reflectionPropertyMock = $this->createMock(ClassPropertyReflectionInterface::class);
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
        $reflectionConstantMock = $this->createMock(ClassConstantReflectionInterface::class);
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
