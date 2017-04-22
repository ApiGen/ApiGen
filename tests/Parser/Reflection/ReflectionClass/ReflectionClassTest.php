<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Reflection\ReflectionClass;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Tests\Parser\Reflection\ReflectionClassSource\AccessLevels;
use ApiGen\Tests\Parser\Reflection\ReflectionClassSource\ParentClass;

final class ReflectionClassTest extends AbstractReflectionClassTestCase
{
    public function testInterface(): void
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->reflectionClass);
    }

    public function testGetName(): void
    {
        $this->assertSame(AccessLevels::class, $this->reflectionClass->getName());
    }

    public function testGetShortName(): void
    {
        $this->assertSame('AccessLevels', $this->reflectionClass->getShortName());
    }

    public function testIsAbstract(): void
    {
        $this->assertFalse($this->reflectionClass->isAbstract());
    }

    public function testIsFinal(): void
    {
        $this->assertTrue($this->reflectionClass->isFinal());
    }

    public function testIsException(): void
    {
        $this->assertFalse($this->reflectionClass->isException());
    }

    public function testIsSubclassOf(): void
    {
        $this->assertTrue($this->reflectionClass->isSubclassOf(ParentClass::class));
        $this->assertFalse($this->reflectionClass->isSubclassOf('ArrayAccess'));
    }

    public function testIsDocumented(): void
    {
        $this->assertTrue($this->reflectionClass->isDocumented());
    }

    public function testVisibility(): void
    {
        $this->assertTrue($this->reflectionClass->hasMethod('publicMethod'));
        $this->assertTrue($this->reflectionClass->hasMethod('protectedMethod'));
        $this->assertFalse($this->reflectionClass->hasMethod('privateMethod'));

        $this->assertTrue($this->reflectionClass->hasProperty('publicProperty'));
        $this->assertTrue($this->reflectionClass->hasProperty('protectedProperty'));
        $this->assertFalse($this->reflectionClass->hasProperty('privateProperty'));
    }
}
