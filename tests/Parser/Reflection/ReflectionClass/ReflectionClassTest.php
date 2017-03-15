<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflections\ReflectionClass;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Parser\Tests\Reflection\ReflectionClass\AbstractReflectionClassTestCase;

class ReflectionClassTest extends AbstractReflectionClassTestCase
{

    public function testInterface()
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->reflectionClass);
    }


    public function testGetName()
    {
        $this->assertSame('Project\AccessLevels', $this->reflectionClass->getName());
    }


    public function testGetShortName()
    {
        $this->assertSame('AccessLevels', $this->reflectionClass->getShortName());
    }


    public function testIsAbstract()
    {
        $this->assertFalse($this->reflectionClass->isAbstract());
    }


    public function testIsFinal()
    {
        $this->assertFalse($this->reflectionClass->isFinal());
    }


    public function testIsException()
    {
        $this->assertFalse($this->reflectionClass->isException());
    }


    public function testIsSubclassOf()
    {
        $this->assertTrue($this->reflectionClass->isSubclassOf('Project\ParentClass'));
        $this->assertFalse($this->reflectionClass->isSubclassOf('ArrayAccess'));
    }


    public function testIsValid()
    {
        $this->assertTrue($this->reflectionClass->isValid());
    }


    public function testIsDocumented()
    {
        $this->assertTrue($this->reflectionClass->isDocumented());
    }


    public function testVisibility()
    {
        $this->assertTrue($this->reflectionClass->hasMethod('publicMethod'));
        $this->assertTrue($this->reflectionClass->hasMethod('protectedMethod'));
        $this->assertFalse($this->reflectionClass->hasMethod('privateMethod'));

        $this->assertTrue($this->reflectionClass->hasProperty('publicProperty'));
        $this->assertTrue($this->reflectionClass->hasProperty('protectedProperty'));
        $this->assertFalse($this->reflectionClass->hasProperty('privateProperty'));
    }
}
