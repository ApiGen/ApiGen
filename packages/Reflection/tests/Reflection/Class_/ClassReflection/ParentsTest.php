<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Reflection\ReflectionClass;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\ParentClass;

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

    public function testGetDirectSubClasses(): void
    {
        $this->assertCount(1, $this->reflectionClassOfParent->getDirectSubClasses());
    }

    public function testIndirectSubClasses(): void
    {
        $this->assertCount(0, $this->reflectionClassOfParent->getIndirectSubClasses());
    }
}
