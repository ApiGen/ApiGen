<?php

namespace ApiGen\Tests\Reflections\ReflectionClass;

use ApiGen\Reflection\ReflectionClass;
use Project\RichInterface;
use TokenReflection;


class InterfacesTest extends TestCase
{

	public function testIsInterface()
	{
		$this->assertFalse($this->reflectionClass->isInterface());
	}


	public function testImplementsInterface()
	{
		$this->assertFalse($this->reflectionClass->implementsInterface('NoInterface'));
		$this->assertTrue($this->reflectionClass->implementsInterface(RichInterface::class));
	}


	public function testGetInterfaces()
	{
		$interfaces = $this->reflectionClass->getInterfaces();
		$this->assertCount(1, $interfaces);
		$this->assertInstanceOf(ReflectionClass::class, $interfaces[RichInterface::class]);
	}


	public function testGetOwnInterfaces()
	{
		$interfaces = $this->reflectionClass->getOwnInterfaces();
		$this->assertCount(1, $interfaces);
		$this->assertInstanceOf(ReflectionClass::class, $interfaces[RichInterface::class]);
	}


	public function testGetOwnInterfaceNames()
	{
		$this->assertSame([RichInterface::class], $this->reflectionClass->getOwnInterfaceNames());
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
