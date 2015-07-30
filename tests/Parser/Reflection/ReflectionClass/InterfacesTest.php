<?php

namespace ApiGen\Parser\Tests\Reflections\ReflectionClass;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Parser\Tests\Reflection\ReflectionClass\AbstractReflectionClassTestCase;
use TokenReflection;

class InterfacesTest extends AbstractReflectionClassTestCase
{

    public function testIsInterface()
    {
        $this->assertFalse($this->reflectionClass->isInterface());
    }


    public function testImplementsInterface()
    {
        $this->assertFalse($this->reflectionClass->implementsInterface('NoInterface'));
        $this->assertTrue($this->reflectionClass->implementsInterface('Project\RichInterface'));
    }


    public function testGetInterfaces()
    {
        $interfaces = $this->reflectionClass->getInterfaces();
        $this->assertCount(1, $interfaces);
        $this->assertInstanceOf(ClassReflectionInterface::class, $interfaces['Project\RichInterface']);
    }


    public function testGetOwnInterfaces()
    {
        $interfaces = $this->reflectionClass->getOwnInterfaces();
        $this->assertCount(1, $interfaces);
        $this->assertInstanceOf(ClassReflectionInterface::class, $interfaces['Project\RichInterface']);
    }


    public function testGetOwnInterfaceNames()
    {
        $this->assertSame(['Project\RichInterface'], $this->reflectionClass->getOwnInterfaceNames());
    }


    public function testGetDirectImplementers()
    {
        $this->assertCount(1, $this->reflectionClassOfInterface->getDirectImplementers());
    }


    public function testGetIndirectImplementers()
    {
        $indirectImplementers = $this->reflectionClassOfInterface->getIndirectImplementers();
        $this->assertSame([], $indirectImplementers);
    }
}
