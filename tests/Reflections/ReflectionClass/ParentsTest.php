<?php

namespace ApiGen\Tests\Reflections\ReflectionClass;

use ApiGen\Reflection\ReflectionClass;
use Project\ParentClass;


class ParentsTest extends TestCase
{

	public function testGetParentClass()
	{
		$this->assertInstanceOf(ReflectionClass::class, $this->reflectionClass->getParentClass());
	}


	public function testGetParentClassName()
	{
		$this->assertSame(ParentClass::class, $this->reflectionClass->getParentClassName());
	}


	public function testGetParentClasses()
	{
		$this->assertCount(1, $this->reflectionClass->getParentClasses());
	}


	public function testGetParentClassNameList()
	{
		$this->assertSame([ParentClass::class], $this->reflectionClass->getParentClassNameList());
	}


	public function testGetDirectSubClasses()
	{
		$this->assertCount(1, $this->reflectionClassOfParent->getDirectSubClasses());
	}


	public function testIndirectSubClasses()
	{
		$this->assertCount(0, $this->reflectionClassOfParent->getIndirectSubClasses());
	}

}
