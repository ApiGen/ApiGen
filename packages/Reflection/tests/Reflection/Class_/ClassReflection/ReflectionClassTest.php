<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\AccessLevels;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\ParentClass;

final class ReflectionClassTest extends AbstractReflectionClassTestCase
{
    public function testInterface(): void
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->reflectionClass);
    }

    public function testName(): void
    {
        $this->assertSame(AccessLevels::class, $this->reflectionClass->getName());
        $this->assertSame('AccessLevels', $this->reflectionClass->getShortName());
    }

    public function testModifiers(): void
    {
        $this->assertFalse($this->reflectionClass->isAbstract());
        $this->assertTrue($this->reflectionClass->isFinal());
    }

    public function testIsSubclassOf(): void
    {
        $this->assertTrue($this->reflectionClass->isSubclassOf(ParentClass::class));
        $this->assertFalse($this->reflectionClass->isSubclassOf('ArrayAccess'));
    }

    public function testVisibility(): void
    {
        $this->assertCount(5, $this->reflectionClass->getMethods());
    }
}
