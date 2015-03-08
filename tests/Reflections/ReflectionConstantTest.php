<?php

namespace ApiGen\Tests\Reflection;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit_Framework_TestCase;
use TokenReflection\Broker;


class ReflectionConstantTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ReflectionConstant
	 */
	private $constantReflection;

	/**
	 * @var ReflectionConstant
	 */
	private $constantReflectionInClass;

	/**
	 * @var ReflectionClass
	 */
	private $reflectionClass;


	protected function setUp()
	{
		$backend = new Backend($this->getReflectionFactory());
		$broker = new Broker($backend);
		$broker->processDirectory(__DIR__ . '/ReflectionConstantSource');
		$this->constantReflection = $backend->getConstants()['SOME_CONSTANT'];

		/** @var ReflectionClass $reflectionClass */
		$this->reflectionClass = $backend->getClasses()['ConstantInClass'];
		$this->constantReflectionInClass = $this->reflectionClass->getConstant('CONSTANT_INSIDE');
	}


	public function testInstance()
	{
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionConstant', $this->constantReflection);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionConstant', $this->constantReflectionInClass);
	}


	public function testGetDeclaringClass()
	{
		$this->assertNull($this->constantReflection->getDeclaringClass());
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionClass', $this->constantReflectionInClass->getDeclaringClass());
	}


	public function testGetDeclaringClassName()
	{
		$this->assertNull($this->constantReflection->getDeclaringClassName());
		$this->assertSame('ConstantInClass', $this->constantReflectionInClass->getDeclaringClassName());
	}


	public function testGetName()
	{
		$this->assertSame('SOME_CONSTANT', $this->constantReflection->getName());
		$this->assertSame('CONSTANT_INSIDE', $this->constantReflectionInClass->getName());
	}


	public function testGetShortName()
	{
		$this->assertSame('SOME_CONSTANT', $this->constantReflection->getShortName());
		$this->assertSame('CONSTANT_INSIDE', $this->constantReflectionInClass->getShortName());
	}


	public function testGetTypeHint()
	{
		$this->assertSame('string', $this->constantReflection->getTypeHint());
		$this->assertSame('int', $this->constantReflectionInClass->getTypeHint());
	}


	public function testGetValue()
	{
		$this->assertSame('some value', $this->constantReflection->getValue());
		$this->assertSame(55, $this->constantReflectionInClass->getValue());
	}


	public function testGetDefinition()
	{
		$this->assertSame("'some value'", $this->constantReflection->getValueDefinition());
		$this->assertSame('55', $this->constantReflectionInClass->getValueDefinition());
	}


	public function testIsValid()
	{
		$this->assertTrue($this->constantReflection->isValid());
		$this->assertTrue($this->constantReflectionInClass->isValid());
	}


	public function testIsDocumented()
	{
		$this->assertTrue($this->constantReflection->isDocumented());
		$this->assertTrue($this->constantReflectionInClass->isDocumented());
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionFactory()
	{
		$parserResultMock = Mockery::mock('ApiGen\Parser\ParserResult');
		$parserResultMock->shouldReceive('getElementsByType')->andReturnUsing(function ($arg) {
			if ($arg) {
				return ['ConstantInClass' => $this->reflectionClass];
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
		$configurationMock->shouldReceive('getOption')->with(CO::VISIBILITY_LEVELS)->andReturn(1);
		return $configurationMock;
	}

}
