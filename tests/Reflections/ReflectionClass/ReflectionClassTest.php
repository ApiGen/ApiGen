<?php

namespace ApiGen\Tests\Reflections\ReflectionClass;


class ReflectionClassTest extends TestCase
{

	public function testInterface()
	{
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionClass', $this->reflectionClass);
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


	public function testIsInterface()
	{
		$this->assertFalse($this->reflectionClass->isInterface());
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
