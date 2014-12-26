<?php

namespace ApiGen\Tests\Reflections\ReflectionClass;


class TraitsTest extends TestCase
{

	public function testGetTraits()
	{
		$this->assertCount(1, $this->reflectionClass->getTraits());
	}


	public function testGetTraitNames()
	{
		$this->assertSame(['Project\SomeTrait'], $this->reflectionClass->getTraitNames());
	}


	public function testGetTraitAliases()
	{
		$this->assertCount(0, $this->reflectionClass->getTraitAliases());
	}


	public function testGetTraitMethods()
	{
		$this->assertCount(0, $this->reflectionClass->getTraitMethods());
	}


	public function testGetTraitProperties()
	{
		$this->assertCount(0, $this->reflectionClass->getTraitProperties());
	}

}
