<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Tests\Route;

use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ReflectionRouteTest extends AbstractContainerAwareTestCase
{
    /**
     * @var
     */
    private $reflectionRoute;

    protected function setUp(): void
    {
        $this->reflectionRoute = $this->container->getByType(ReflectionRoute::class);
    }

//    public function testClassUrl(): void
//    {
//        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
//        $reflectionClassMock->method('getName')
//            ->willReturn('SomeClass');
//
//        $this->assertSame(
//            'class-SomeClass.html',
//            $this->linkReflectionFilters->classUrl($reflectionClassMock)
//        );
//    }
//
//    public function testMethodUrl(): void
//    {
//        $reflectionMethodMock = $this->createMock(ClassMethodReflectionInterface::class);
//        $reflectionMethodMock->method('getDeclaringClassName')
//            ->willReturn('SomeClass');
//        $reflectionMethodMock->method('getName')
//            ->willReturn('SomeMethod');
//
//        $this->assertSame(
//            'class-SomeClass.html#_SomeMethod',
//            $this->linkReflectionFilters->methodUrl($reflectionMethodMock)
//        );
//    }
//
//    public function testPropertyUrl(): void
//    {
//        $reflectionPropertyMock = $this->createMock(ClassPropertyReflectionInterface::class);
//        $reflectionPropertyMock->method('getDeclaringClassName')
//            ->willReturn('SomeClass');
//        $reflectionPropertyMock->method('getName')
//            ->willReturn('SomeProperty');
//
//        $this->assertSame(
//            'class-SomeClass.html#$SomeProperty',
//            $this->linkReflectionFilters->propertyUrl($reflectionPropertyMock)
//        );
//    }
//
//    public function testConstantUrl(): void
//    {
//        $reflectionConstantMock = $this->createMock(ClassConstantReflectionInterface::class);
//        $reflectionConstantMock->method('getDeclaringClassName')
//            ->willReturn('SomeClass');
//        $reflectionConstantMock->method('getName')
//            ->willReturn('SomeConstant');
//
//        $this->assertSame(
//            'class-SomeClass.html#SomeConstant',
//            $this->linkReflectionFilters->constantUrl($reflectionConstantMock)
//        );
//    }
//
//    public function testFunctionUrl(): void
//    {
//        $reflectionFunctionMock = $this->createMock(FunctionReflectionInterface::class);
//        $reflectionFunctionMock->method('getName')
//            ->willReturn('SomeFunction');
//
//        $this->assertSame('function-SomeFunction.html', $this->linkReflectionFilters->functionUrl($reflectionFunctionMock));
//    }
}
