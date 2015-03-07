<?php

namespace ApiGen\Tests\Reflections\ReflectionClass;

use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\ReflectionMethodMagic;
use InvalidArgumentException;


class MethodsTest extends TestCase
{

	public function testGetMethod()
	{
		$this->assertInstanceOf(ReflectionMethod::class, $this->reflectionClass->getMethod('publicMethod'));
	}


	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testGetMethodNonExisting()
	{
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
		$this->assertCount(3, $this->reflectionClass->getMagicMethods());
		$magicMethod = $this->reflectionClass->getMagicMethods()['getSome'];
		$this->assertInstanceOf(ReflectionMethodMagic::class, $magicMethod);
	}


	public function testGetOwnMagicMethods()
	{
		$this->assertCount(1, $this->reflectionClass->getOwnMagicMethods());
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
