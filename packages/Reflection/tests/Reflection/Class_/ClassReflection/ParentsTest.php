<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\ParentClass;
use ArrayAccess;

final class ParentsTest extends AbstractReflectionClassTestCase
{
    public function testGetParentClass(): void
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->reflectionClass->getParentClass());
    }

    public function testGetParentClassName(): void
    {
        $this->assertSame(ParentClass::class, $this->reflectionClass->getParentClassName());
    }

    public function testGetParentClasses(): void
    {
        $this->assertCount(1, $this->reflectionClass->getParentClasses());
    }

    public function testGetSubClasses(): void
    {
        $this->assertCount(2, $this->reflectionClassOfParent->getSubClasses());
    }

    public function testIsSubclassOf(): void
    {
        $this->assertTrue($this->reflectionClass->isSubclassOf(ParentClass::class));
        $this->assertFalse($this->reflectionClass->isSubclassOf(ArrayAccess::class));
    }
}
