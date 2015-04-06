<?php

namespace ApiGen\Tests\Reflection;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\ParserResult;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit_Framework_TestCase;
use Project;
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

		$this->reflectionClass = $backend->getClasses()[Project\ReflectionMethod::class];
		$this->reflectionMethod = $this->reflectionClass->getMethod('methodWithArgs');
	}


	public function testGetDeclaringClass()
	{
		$this->isInstanceOf(ReflectionClass::class, $this->reflectionMethod->getDeclaringClass());
	}


	public function testGetDeclaringClassName()
	{
		$this->assertSame(Project\ReflectionMethod::class, $this->reflectionMethod->getDeclaringClassName());
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


	public function testIsStatic()
	{
		$this->assertFalse($this->reflectionMethod->isStatic());
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
		$parserResultMock = Mockery::mock(ParserResult::class);
		$parserResultMock->shouldReceive('getElementsByType')->andReturnUsing(function ($arg) {
			if ($arg) {
				return [Project\ReflectionMethod::class => $this->reflectionClass];
			}
		});
		return new ReflectionFactory($this->getConfigurationMock(), $parserResultMock);
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getConfigurationMock()
	{
		$configurationMock = Mockery::mock(Configuration::class);
		$configurationMock->shouldReceive('getOption')->with('php')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('deprecated')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('internal')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with(CO::VISIBILITY_LEVELS)->andReturn(256);
		return $configurationMock;
	}

}
