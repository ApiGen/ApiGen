<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Reflection\ReflectionClass;

use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Tests\Parser\Reflection\ReflectionClassSource\RichInterface;

final class InterfacesTest extends AbstractReflectionClassTestCase
{
    public function testIsInterface(): void
    {
        $this->assertFalse($this->reflectionClass->isInterface());
    }

    public function testImplementsInterface(): void
    {
        $this->assertFalse($this->reflectionClass->implementsInterface('NoInterface'));
        $this->assertTrue($this->reflectionClass->implementsInterface(RichInterface::class));
    }

    public function testGetInterfaces(): void
    {
        $interfaces = $this->reflectionClass->getInterfaces();
        $this->assertCount(1, $interfaces);
        $this->assertInstanceOf(ClassReflectionInterface::class, $interfaces[RichInterface::class]);
    }

    public function testGetOwnInterfaces(): void
    {
        $interfaces = $this->reflectionClass->getOwnInterfaces();
        $this->assertCount(1, $interfaces);
        $this->assertInstanceOf(ClassReflectionInterface::class, $interfaces[RichInterface::class]);
    }

    public function testGetOwnInterfaceNames(): void
    {
        $this->assertSame([RichInterface::class], $this->reflectionClass->getOwnInterfaceNames());
    }

    public function testGetDirectImplementers(): void
    {
        $this->assertCount(1, $this->reflectionClassOfInterface->getDirectImplementers());
    }

    public function testGetIndirectImplementers(): void
    {
        $indirectImplementers = $this->reflectionClassOfInterface->getIndirectImplementers();
        $this->assertSame([], $indirectImplementers);
    }
}
