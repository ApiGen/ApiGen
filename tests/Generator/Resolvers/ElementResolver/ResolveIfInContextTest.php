<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassPropertyReflectionInterface;
use ApiGen\Tests\MethodInvoker;
use PHPUnit_Framework_MockObject_MockObject;

final class ResolveIfInContextTest extends AbstractElementResolverTest
{
    public function testResolvePropertyFromClassReflection(): void
    {
        $classReflectionMock = $this->createClassReflectionMockWithProperty();

        $resolvedElement = MethodInvoker::callMethodOnObject(
            $this->elementResolver,
            'resolveIfInContext',
            ['$someProperty', $classReflectionMock]
        );

        $this->assertInstanceOf(ClassPropertyReflectionInterface::class, $resolvedElement);
    }

    public function testResolveMethodFromClassReflection(): void
    {
        $classReflectionMock = $this->createClassReflectionMockWithMethod();

        $resolvedElement = MethodInvoker::callMethodOnObject(
            $this->elementResolver,
            'resolveIfInContext',
            ['someMethod', $classReflectionMock]
        );

        $this->assertInstanceOf(ClassMethodReflectionInterface::class, $resolvedElement);
    }

    public function testResolveConstantFromClassReflection(): void
    {
        $classReflectionMock = $this->createClassReflectionMockWithConstant();

        $resolvedElement = MethodInvoker::callMethodOnObject(
            $this->elementResolver,
            'resolveIfInContext',
            ['someConstant', $classReflectionMock]
        );

        $this->assertInstanceOf(ClassConstantReflectionInterface::class, $resolvedElement);
    }

    public function testMissingElement(): void
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);

        $resolvedElement = MethodInvoker::callMethodOnObject(
            $this->elementResolver,
            'resolveIfInContext',
            ['missingElement', $classReflectionMock]
        );

        $this->assertNull($resolvedElement);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ClassReflectionInterface
     */
    private function createClassReflectionMockWithProperty()
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);

        $classReflectionMock->method('hasProperty')
            ->with('someProperty')
            ->willReturn(true);

        $propertyReflectionMock = $this->createMock(ClassPropertyReflectionInterface::class);
        $classReflectionMock->method('getProperty')
            ->with('someProperty')
            ->willReturn($propertyReflectionMock);

        return $classReflectionMock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ClassReflectionInterface
     */
    public function createClassReflectionMockWithMethod()
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);

        $classReflectionMock->method('hasMethod')
            ->with('someMethod')
            ->willReturn(true);

        $classReflectionMock->method('hasMethod')
            ->willReturn(false);

        $methodReflectionMock = $this->createMock(ClassMethodReflectionInterface::class);
        $classReflectionMock->method('getMethod')
            ->with('someMethod')
            ->willReturn($methodReflectionMock);

        return $classReflectionMock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ClassReflectionInterface
     */
    private function createClassReflectionMockWithConstant()
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);

        $classReflectionMock->method('hasConstant')
            ->with('someConstant')
            ->willReturn(true);

        $classReflectionMock->method('hasConstant')
            ->willReturn(false);

        $constantReflectionMock = $this->createMock(ClassConstantReflectionInterface::class);

        $classReflectionMock->method('getConstant')
            ->willReturn($constantReflectionMock);

        return $classReflectionMock;
    }
}
