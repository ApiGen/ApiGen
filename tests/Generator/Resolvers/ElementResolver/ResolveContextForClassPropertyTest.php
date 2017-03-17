<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Tests\MethodInvoker;
use PHPUnit_Framework_MockObject_MockObject;

final class ResolveContextForClassPropertyTest extends AbstractElementResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $parentClassReflectionMock = $this->createParentClassReflectionMock();

        $this->parserStorage->setClasses([
            'SomeClass' => $parentClassReflectionMock
        ]);
    }


    public function testPropertyInParent(): void
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $classReflectionMock->method('getParentClassName')
            ->willReturn('SomeClass');

        $resolvedElement = MethodInvoker::callMethodOnObject($this->elementResolver, 'resolveContextForClassProperty', [
            'parent::$start', $classReflectionMock, 5
        ]);

        $this->assertInstanceOf(ClassReflectionInterface::class, $resolvedElement);
        $this->assertSame('SomeClass', $resolvedElement->getName());
    }


    public function testPropertyInSelf(): void
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $classReflectionMock->method('getName')
            ->willReturn('ChildClass');
        $classReflectionMock->method('getParentClassName')
            ->willReturn('SomeClass');

        $resolvedElement = MethodInvoker::callMethodOnObject($this->elementResolver, 'resolveContextForClassProperty', [
            'self::$start', $classReflectionMock, 25
        ]);

        $this->assertInstanceOf(ClassReflectionInterface::class, $resolvedElement);
        $this->assertSame('ChildClass', $resolvedElement->getName());
    }


    public function testNonExisting(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);

        $resolvedElement = MethodInvoker::callMethodOnObject($this->elementResolver, 'resolveContextForClassProperty', [
            '$start', $reflectionClassMock, 25
        ]);
        $this->assertNull($resolvedElement);
    }


    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ClassReflectionInterface
     */
    private function createParentClassReflectionMock()
    {
        $parentClassReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $parentClassReflectionMock->method('getName')
            ->willReturn('SomeClass');

        $parentClassReflectionMock->method('isDocumented')
            ->willReturn(true);

        return $parentClassReflectionMock;
    }
}
