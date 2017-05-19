<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters\Helpers;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassPropertyReflectionInterface;
use ApiGen\Templating\Filters\Helpers\linkReflectionFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

final class linkReflectionFactoryTest extends TestCase
{
    /**
     * @var linkReflectionFactory
     */
    private $linkReflectionFactory;

    protected function setUp(): void
    {
        $this->linkReflectionFactory = new linkReflectionFactory;
    }

    public function testCreateForElement(): void
    {
        $this->assertSame(
            'class-SomeNamespace.SomeClass.html',
            $this->linkReflectionFactory->createForElement($this->getReflectionClassMock())
        );

        $reflectionMethodMock = $this->getReflectionMethodMock();
        $reflectionMethodMock->method('getOriginalName')
            ->willReturn('');

        $this->assertSame(
            'class-SomeClass.html#_getSomeMethod',
            $this->linkReflectionFactory->createForElement($reflectionMethodMock)
        );

        $reflectionPropertyMock = $this->getReflectionPropertyMock();
        $this->assertSame(
            'class-SomeClass.html#$someProperty',
            $this->linkReflectionFactory->createForElement($reflectionPropertyMock)
        );

        $reflectionConstantMock = $this->getReflectionConstantMock();
        $reflectionConstantMock->method('getDeclaringClassName')
            ->willReturn('SomeClass');

        $this->assertSame(
            'class-SomeClass.html#someConstant',
            $this->linkReflectionFactory->createForElement($reflectionConstantMock)
        );

        $this->assertSame(
            'function-someFunction.html',
            $this->linkReflectionFactory->createForElement($this->getReflectionFunctionMock())
        );

        $reflectionElementMock = $this->createMock(ReflectionInterface::class);
        $this->assertNull($this->linkReflectionFactory->createForElement($reflectionElementMock));
    }

    public function testCreateForClass(): void
    {
        $this->assertSame(
            'class-SomeNamespace.SomeClass.html',
            $this->linkReflectionFactory->createForClass($this->getReflectionClassMock())
        );
        $this->assertSame(
            'class-SomeStringClass.html',
            $this->linkReflectionFactory->createForClass('SomeStringClass')
        );
    }

    public function testCreateForMethod(): void
    {
        $reflectionMethodMock = $this->getReflectionMethodMock();

        $reflectionMethodMock->method('getOriginalName')
            ->willReturn('getSomeMethodOriginal');
        $this->assertSame(
            'class-SomeClass.html#_getSomeMethodOriginal',
            $this->linkReflectionFactory->createForMethod($reflectionMethodMock)
        );
    }

    public function testCreateForMethodAnother(): void
    {
        $reflectionMethodMock = $this->getReflectionMethodMock();

        $reflectionMethodMock->method('getOriginalName')
            ->willReturn('');

        $this->assertSame(
            'class-SomeClass.html#_getSomeMethod',
            $this->linkReflectionFactory->createForMethod($reflectionMethodMock)
        );
    }

    public function testCreateForMethodWithSeparateClass(): void
    {
        $reflectionMethodMock = $this->getReflectionMethodMock();
        $reflectionMethodMock->method('getOriginalName')
            ->willReturn('');

        $this->assertSame(
            'class-SomeNamespace.SomeClass.html#_getSomeMethod',
            $this->linkReflectionFactory->createForMethod($reflectionMethodMock, $this->getReflectionClassMock())
        );
    }

    public function testCreateForProperty(): void
    {
        $reflectionPropertyMock = $this->getReflectionPropertyMock();

        $this->assertSame(
            'class-SomeClass.html#$someProperty',
            $this->linkReflectionFactory->createForProperty($reflectionPropertyMock)
        );
    }

    public function testCreateForPropertyWithSeparateClass(): void
    {
        $reflectionPropertyMock = $this->getReflectionPropertyMock();

        $this->assertSame(
            'class-SomeNamespace.SomeClass.html#$someProperty',
            $this->linkReflectionFactory->createForProperty($reflectionPropertyMock, $this->getReflectionClassMock())
        );
    }

    public function testCreateForConstantInClass(): void
    {
        $reflectionConstantMock = $this->getReflectionConstantMock();
        $reflectionConstantMock->method('getDeclaringClassName')
            ->willReturn('SomeClass');

        $this->assertSame(
            'class-SomeClass.html#someConstant',
            $this->linkReflectionFactory->createForConstant($reflectionConstantMock)
        );
    }

    public function testCreateForFunction(): void
    {
        $reflectionFunctionMock = $this->getReflectionFunctionMock();

        $this->assertSame(
            'function-someFunction.html',
            $this->linkReflectionFactory->createForFunction($reflectionFunctionMock)
        );
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ClassMethodReflectionInterface
     */
    private function getReflectionMethodMock()
    {
        $reflectionMethodMock = $this->createMock(ClassMethodReflectionInterface::class);
        $reflectionMethodMock->method('getName')
            ->willReturn('getSomeMethod');
        $reflectionMethodMock->method('getDeclaringClassName')
            ->willReturn('SomeClass');

        return $reflectionMethodMock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ClassReflectionInterface
     */
    private function getReflectionClassMock()
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $reflectionClassMock->method('getName')
            ->willReturn('SomeNamespace\\SomeClass');
        return $reflectionClassMock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ClassPropertyReflectionInterface
     */
    private function getReflectionPropertyMock()
    {
        $reflectionPropertyMock = $this->createMock(ClassPropertyReflectionInterface::class);
        $reflectionPropertyMock->method('getName')
            ->willReturn('someProperty');
        $reflectionPropertyMock->method('getDeclaringClassName')
            ->willReturn('SomeClass');
        return $reflectionPropertyMock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ClassConstantReflectionInterface
     */
    private function getReflectionConstantMock()
    {
        $reflectionConstantMock = $this->createMock(ClassConstantReflectionInterface::class);
        $reflectionConstantMock->method('getName')
            ->willReturn('someConstant');

        return $reflectionConstantMock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|FunctionReflectionInterface
     */
    private function getReflectionFunctionMock()
    {
        $reflectionFunctionMock = $this->createMock(FunctionReflectionInterface::class);
        $reflectionFunctionMock->method('getName')
            ->willReturn('someFunction');

        return $reflectionFunctionMock;
    }
}
