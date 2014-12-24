<?php

namespace ApiGen\Tests\Reflection;

use ApiGen\Parser\Broker\Backend;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit_Framework_TestCase;
use TokenReflection\Broker;


class ReflectionMethodTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ReflectionMethod
	 */
	private $reflectionMethod;

	/**
	 * @var ReflectionClass
	 */
	private $reflectionClass;


	protected function setUp()
	{
		$backend = new Backend($this->getReflectionFactory());
		$broker = new Broker($backend);
		$broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

		$this->reflectionClass = $backend->getClasses()['Project\ReflectionMethod'];
		$this->reflectionMethod = $this->reflectionClass->getMethod('methodWithArgs');
	}


	public function testGetDeclaringClass()
	{
		$this->isInstanceOf('ApiGen\Reflection\ReflectionClass', $this->reflectionMethod->getDeclaringClass());
	}


	public function testGetDeclaringClassName()
	{
		$this->assertSame('Project\ReflectionMethod', $this->reflectionMethod->getDeclaringClassName());
	}


	public function testIsAbstract()
	{
		$this->assertFalse($this->reflectionMethod->isAbstract());
	}


	public function testIsFinal()
	{
		$this->assertFalse($this->reflectionMethod->isFinal());
	}


	public function testIsPrivate()
	{
		$this->assertFalse($this->reflectionMethod->isPrivate());
	}


	public function testIsProtected()
	{
		$this->assertFalse($this->reflectionMethod->isProtected());
	}


	public function testIsPublic()
	{
		$this->assertTrue($this->reflectionMethod->isPublic());
	}


	public function testIsConstructor()
	{
		$this->assertFalse($this->reflectionMethod->isConstructor());
	}


	public function testIsDestructor()
	{
		$this->assertFalse($this->reflectionMethod->isDestructor());
	}


	public function testGetDeclaringTrait()
	{
		$this->assertNull($this->reflectionMethod->getDeclaringTrait());
	}


	public function testGetDeclaringTraitName()
	{
		$this->assertNull($this->reflectionMethod->getDeclaringTraitName());
	}


	public function testGetImplementedMethod()
	{
		$this->assertNull($this->reflectionMethod->getImplementedMethod());
	}


	public function testGetOverriddenMethod()
	{
		$this->assertNull($this->reflectionMethod->getOverriddenMethod());
	}


	public function testGetOriginal()
	{
		$this->assertNull($this->reflectionMethod->getOriginal());
	}


	public function testGetOriginalName()
	{
		$this->assertNull($this->reflectionMethod->getOriginalName());
	}


	public function testIsValid()
	{
		$this->assertTrue($this->reflectionMethod->isValid());
	}


	/** ReflectionFunctionBase methods */

	public function testGetParameters()
	{
		$this->assertCount(3, $this->reflectionMethod->getParameters());
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionFactory()
	{
		$parserResultMock = Mockery::mock('ApiGen\Parser\ParserResult');
		$parserResultMock->shouldReceive('getElementsByType')->andReturnUsing(function ($arg) {
			if ($arg) {
				return ['Project\ReflectionMethod' => $this->reflectionClass];
			}
		});
		return new ReflectionFactory($this->getConfigurationMock(), $parserResultMock);
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getConfigurationMock()
	{
		$configurationMock = Mockery::mock('ApiGen\Configuration\Configuration');
		$configurationMock->shouldReceive('getOption')->with('php')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('deprecated')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('internal')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('skipDocPath')->andReturn(['*SomeConstant.php*']);
		$configurationMock->shouldReceive('getOption')->with('propertyAndMethodAccessLevels')->andReturn(256);
		return $configurationMock;
	}

}
