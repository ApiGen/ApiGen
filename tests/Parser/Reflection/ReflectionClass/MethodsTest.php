<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflections\ReflectionClass;

use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Parser\Tests\Reflection\ReflectionClass\AbstractReflectionClassTestCase;

class MethodsTest extends AbstractReflectionClassTestCase
{

    public function testGetMethod(): void
    {
        $this->assertInstanceOf(MethodReflectionInterface::class, $this->reflectionClass->getMethod('publicMethod'));
    }


    public function testGetMethodNonExisting(): void
    {
        $this->reflectionClass->getMethod('notPresentMethod');
    }


    public function testGetMethods(): void
    {
        $this->assertCount(5, $this->reflectionClass->getMethods());
    }


    public function testGetOwnMethods(): void
    {
        $this->assertCount(2, $this->reflectionClass->getOwnMethods());
    }


    public function testGetMagicMethods(): void
    {
        $this->assertCount(4, $this->reflectionClass->getMagicMethods());
        $magicMethod = $this->reflectionClass->getMagicMethods()['getSome'];
        $this->assertInstanceOf(MagicMethodReflectionInterface::class, $magicMethod);
    }


    public function testGetOwnMagicMethods(): void
    {
        $this->assertCount(2, $this->reflectionClass->getOwnMagicMethods());
    }


    public function testGetInheritedMethods(): void
    {
        $this->assertCount(2, $this->reflectionClass->getInheritedMethods());
    }


    public function testGetInheritedMagicMethods(): void
    {
        $this->assertCount(1, $this->reflectionClass->getInheritedMagicMethods());
    }


    public function testGetUsedMethods(): void
    {
        $this->assertCount(1, $this->reflectionClass->getUsedMethods());
    }


    public function testGetUsedMagicMethods(): void
    {
        $this->assertCount(1, $this->reflectionClass->getUsedMagicMethods());
    }
}
