<?php

namespace ApiGen\Parser\Tests\Reflections\ReflectionClass;

use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Parser\Tests\Reflection\ReflectionClass\AbstractReflectionClassTestCase;

class MethodsTest extends AbstractReflectionClassTestCase
{

    public function testGetMethod()
    {
        $this->assertInstanceOf(MethodReflectionInterface::class, $this->reflectionClass->getMethod('publicMethod'));
    }


    public function testGetMethodNonExisting()
    {
        $this->expectException('InvalidArgumentException');
        $this->reflectionClass->getMethod('notPresentMethod');
    }


    public function testGetMethods()
    {
        $this->assertCount(5, $this->reflectionClass->getMethods());
    }


    public function testGetOwnMethods()
    {
        $this->assertCount(2, $this->reflectionClass->getOwnMethods());
    }


    public function testGetMagicMethods()
    {
        $this->assertCount(4, $this->reflectionClass->getMagicMethods());
        $magicMethod = $this->reflectionClass->getMagicMethods()['getSome'];
        $this->assertInstanceOf(MagicMethodReflectionInterface::class, $magicMethod);
    }


    public function testGetOwnMagicMethods()
    {
        $this->assertCount(2, $this->reflectionClass->getOwnMagicMethods());
    }


    public function testGetInheritedMethods()
    {
        $this->assertCount(2, $this->reflectionClass->getInheritedMethods());
    }


    public function testGetInheritedMagicMethods()
    {
        $this->assertCount(1, $this->reflectionClass->getInheritedMagicMethods());
    }


    public function testGetUsedMethods()
    {
        $this->assertCount(1, $this->reflectionClass->getUsedMethods());
    }


    public function testGetUsedMagicMethods()
    {
        $this->assertCount(1, $this->reflectionClass->getUsedMagicMethods());
    }
}
