<?php

namespace ApiGen\Tests\Reflection;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\ParserResult;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\ReflectionParameter;
use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit_Framework_TestCase;
use Project;
use TokenReflection\Broker;


class ReflectionParameterTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ReflectionClass
	 */
	private $reflectionClass;

	/**
	 * @var ReflectionParameter
	 */
	private $reflectionParameter;


	protected function setUp()
	{
		$backend = new Backend($this->getReflectionFactory());
		$broker = new Broker($backend);
		$broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

		$this->reflectionClass = $backend->getClasses()[Project\ReflectionMethod::class];
		$reflectionMethod = $this->reflectionClass->getMethod('methodWithArgs');
		$this->reflectionParameter = $reflectionMethod->getParameter(0);
	}


	public function testInstance()
	{
		$this->assertInstanceOf(ReflectionParameter::class, $this->reflectionParameter);
	}


	public function testGetTypeHint()
	{
		$this->assertSame('int|string', $this->reflectionParameter->getTypeHint());
	}


	public function testGetDescription()
	{
		$this->assertSame(' the URL of the API endpoint', $this->reflectionParameter->getDescription());
	}


	public function testGetDefaultValueDefinition()
	{
		$this->assertSame('1', $this->reflectionParameter->getDefaultValueDefinition());
	}


	public function testIsDefaultValueAvailable()
	{
		$this->assertTrue($this->reflectionParameter->isDefaultValueAvailable());
	}


	public function testGetPosition()
	{
		$this->assertSame(0, $this->reflectionParameter->getPosition());
	}


	public function testIsArray()
	{
		$this->assertFalse($this->reflectionParameter->isArray());
	}


	public function testIsCallable()
	{
		$this->assertFalse($this->reflectionParameter->isCallable());
	}


	public function testGetClass()
	{
		$this->assertNull($this->reflectionParameter->getClass());
	}


	public function testGetClassName()
	{
		$this->assertNull($this->reflectionParameter->getClassName());
	}


	public function testAllowsNull()
	{
		$this->assertTrue($this->reflectionParameter->allowsNull());
	}


	public function testIsOptional()
	{
		$this->assertTrue($this->reflectionParameter->isOptional());
	}


	public function testIsPassedByReference()
	{
		$this->assertFalse($this->reflectionParameter->isPassedByReference());
	}


	public function testCanBePassedByValue()
	{
		$this->assertTrue($this->reflectionParameter->canBePassedByValue());
	}


	public function testGetDeclaringFunction()
	{
		$this->assertInstanceOf(ReflectionMethod::class, $this->reflectionParameter->getDeclaringFunction());
	}


	public function testGetDeclaringFunctionName()
	{
		$this->assertSame('methodWithArgs', $this->reflectionParameter->getDeclaringFunctionName());
	}


	public function testGetDeclaringClass()
	{
		$this->assertInstanceOf(ReflectionClass::class, $this->reflectionParameter->getDeclaringClass());
	}


	public function testGetDeclaringClassName()
	{
		$this->assertSame(Project\ReflectionMethod::class, $this->reflectionParameter->getDeclaringClassName());
	}


	public function testIsUnlimited()
	{
		$this->assertFalse($this->reflectionParameter->isUnlimited());
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
		$configurationMock->shouldReceive('getOption')->with('skipDocPath')->andReturn(['*SomeConstant.php*']);
		$configurationMock->shouldReceive('getOption')->with(CO::VISIBILITY_LEVELS)->andReturn(256);
		return $configurationMock;
	}

}
