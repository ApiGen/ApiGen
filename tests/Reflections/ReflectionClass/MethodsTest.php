<?php

namespace ApiGen\Tests\Reflections\ReflectionClass;

use InvalidArgumentException;


class MethodsTest extends TestCase
{

	public function testGetMethod()
	{
		$this->assertInstanceOf(
			'ApiGen\Reflection\ReflectionMethod',
			$this->reflectionClass->getMethod('publicMethod')
		);
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
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionMethodMagic', $magicMethod);
	}


	public function testGetOwnMagicMethods()
	{
		$this->assertCount(1, $this->reflectionClass->getOwnMagicMethods());
	}

}
