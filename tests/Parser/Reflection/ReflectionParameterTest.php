<?php

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Parser\Tests\Configuration\ParserConfiguration;
use Mockery;
use PHPUnit_Framework_TestCase;
use TokenReflection\Broker;


class ReflectionParameterTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ClassReflectionInterface
	 */
	private $reflectionClass;

	/**
	 * @var ParameterReflectionInterface
	 */
	private $reflectionParameter;


	protected function setUp()
	{
		$backend = new Backend($this->getReflectionFactory());
		$broker = new Broker($backend);
		$broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

		$this->reflectionClass = $backend->getClasses()['Project\ReflectionMethod'];
		$reflectionMethod = $this->reflectionClass->getMethod('methodWithArgs');
		$this->reflectionParameter = $reflectionMethod->getParameter(0);
	}


	public function testInstance()
	{
		$this->assertInstanceOf('ApiGen\Parser\Reflection\ReflectionParameter', $this->reflectionParameter);
	}


	public function testGetTypeHint()
	{
		$this->assertSame('int|string', $this->reflectionParameter->getTypeHint());
	}


	public function testGetDescription()
	{
		$this->assertSame(' the URL of the API endpoint', $this->reflectionParameter->getDescription());
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
		$this->assertInstanceOf(MethodReflectionInterface::class, $this->reflectionParameter->getDeclaringFunction());
	}


	public function testGetDeclaringFunctionName()
	{
		$this->assertSame('methodWithArgs', $this->reflectionParameter->getDeclaringFunctionName());
	}


	public function testGetDeclaringClass()
	{
		$this->assertInstanceOf(
			'ApiGen\Parser\Reflection\ReflectionClass',
			$this->reflectionParameter->getDeclaringClass()
		);
	}


	public function testGetDeclaringClassName()
	{
		$this->assertSame('Project\ReflectionMethod', $this->reflectionParameter->getDeclaringClassName());
	}


	public function testIsUnlimited()
	{
		$this->assertFalse($this->reflectionParameter->isUnlimited());
	}


	/**
	 * @return ReflectionFactoryInterface
	 */
	private function getReflectionFactory()
	{
		$parserStorageMock = Mockery::mock(ParserStorageInterface::class);
		$parserStorageMock->shouldReceive('getElementsByType')->andReturnUsing(function ($arg) {
			if ($arg) {
				return ['Project\ReflectionMethod' => $this->reflectionClass];
			}
		});
		return new ReflectionFactory(new ParserConfiguration, $parserStorageMock);
	}

}
