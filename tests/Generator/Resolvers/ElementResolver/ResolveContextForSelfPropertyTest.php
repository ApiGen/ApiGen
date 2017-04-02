<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Tests\MethodInvoker;
use PHPUnit_Framework_MockObject_MockObject;

final class ResolveContextForSelfPropertyTest extends AbstractElementResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $classReflectionMock->method('getName')
            ->willReturn('SomeClass');
        $classReflectionMock->method('isDocumented')
            ->willReturn(true);

        $this->parserStorage->setClasses([
            'SomeClass' => $classReflectionMock,
            'SomeNamespace\SomeOtherClass' => $classReflectionMock
        ]);
    }

    public function testSelfProperty(): void
    {
        $classReflectionMock = $this->createClassReflectionMock();

        $resolvedElement = MethodInvoker::callMethodOnObject(
            $this->elementResolver,
            'resolveContextForSelfProperty',
            ['SomeClass::$property', 9, $classReflectionMock]
        );
        $this->assertInstanceOf(ElementReflectionInterface::class, $resolvedElement);
        $this->assertSame('SomeClass', $resolvedElement->getName());
    }

    public function testOtherClassProperty(): void
    {
        $classReflectionMock = $this->createClassReflectionMock();

        $resolvedElement = MethodInvoker::callMethodOnObject(
            $this->elementResolver,
            'resolveContextForSelfProperty',
            ['SomeOtherClass::$property', 14, $classReflectionMock]
        );
        $this->assertInstanceOf(ElementReflectionInterface::class, $resolvedElement);
        $this->assertSame('SomeClass', $resolvedElement->getName());
    }

    public function testUnknown(): void
    {
        $classReflectionMock = $this->createClassReflectionMock();

        $resolvedElement = MethodInvoker::callMethodOnObject(
            $this->elementResolver,
            'resolveContextForSelfProperty',
            ['NonExistingClass::$property', 14, $classReflectionMock]
        );

        $this->assertNull($resolvedElement);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ClassReflectionInterface
     */
    private function createClassReflectionMock()
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $classReflectionMock->method('getParentClassName')
            ->willReturn('SomeClass');
        $classReflectionMock->method('getNamespaceName')
            ->willReturn('SomeNamespace');

        return $classReflectionMock;
    }
}
