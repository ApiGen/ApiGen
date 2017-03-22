<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
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

        $this->assertInstanceOf(PropertyReflectionInterface::class, $resolvedElement);
    }

    public function testResolveMethodFromClassReflection(): void
    {
        $classReflectionMock = $this->createClassReflectionMockWithMethod();

        $resolvedElement = MethodInvoker::callMethodOnObject(
            $this->elementResolver,
            'resolveIfInContext',
            ['someMethod', $classReflectionMock]
        );

        $this->assertInstanceOf(MethodReflectionInterface::class, $resolvedElement);
    }

    public function testResolveConstantFromClassReflection(): void
    {
        $classReflectionMock = $this->createClassReflectionMockWithConstant();

        $resolvedElement = MethodInvoker::callMethodOnObject(
            $this->elementResolver,
            'resolveIfInContext',
            ['someConstant', $classReflectionMock]
        );

        $this->assertInstanceOf(ConstantReflectionInterface::class, $resolvedElement);
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

        $propertyReflectionMock = $this->createMock(PropertyReflectionInterface::class);
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

        $methodReflectionMock = $this->createMock(MethodReflectionInterface::class);
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

        $constantReflectionMock = $this->createMock(ConstantReflectionInterface::class);

        $classReflectionMock->method('getConstant')
            ->willReturn($constantReflectionMock);

        return $classReflectionMock;
    }
}
