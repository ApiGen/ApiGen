<?php

namespace ApiGen\Tests\Reflection;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\ParserResult;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionProperty;
use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit_Framework_TestCase;
use Project;
use TokenReflection\Broker;


class ReflectionPropertyTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ReflectionClass
	 */
	private $reflectionClass;

	/**
	 * @var ReflectionProperty
	 */
	private $reflectionProperty;


	protected function setUp()
	{
		$backend = new Backend($this->getReflectionFactory());
		$broker = new Broker($backend);
		$broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

		$this->reflectionClass = $backend->getClasses()[Project\ReflectionMethod::class];
		$this->reflectionProperty = $this->reflectionClass->getProperty('memberCount');
	}


	public function testInstance()
	{
		$this->assertInstanceOf(ReflectionProperty::class, $this->reflectionProperty);
	}


	public function testIsReadOnly()
	{
		$this->assertFalse($this->reflectionProperty->isReadOnly());
	}


	public function testIsWriteOnly()
	{
		$this->assertFalse($this->reflectionProperty->isWriteOnly());
	}


	public function testIsMagic()
	{
		$this->assertFalse($this->reflectionProperty->isMagic());
	}


	public function testGetTypeHint()
	{
		$this->assertSame('integer', $this->reflectionProperty->getTypeHint());
	}


	public function testGetDeclaringClass()
	{
		$this->assertInstanceOf(ReflectionClass::class, $this->reflectionProperty->getDeclaringClass());
	}


	public function testGetDeclaringClassName()
	{
		$this->assertSame(Project\ReflectionMethod::class, $this->reflectionProperty->getDeclaringClassName());
	}


	public function testGetDefaultValue()
	{
		$this->assertSame(52, $this->reflectionProperty->getDefaultValue());
	}


	public function testGetDefaultValueDefinition()
	{
		$this->assertSame('52', $this->reflectionProperty->getDefaultValueDefinition());
	}


	public function testIsDefault()
	{
		$this->assertTrue($this->reflectionProperty->isDefault());
	}


	public function testIsPrivate()
	{
		$this->assertFalse($this->reflectionProperty->isPrivate());
	}


	public function testIsProtected()
	{
		$this->assertFalse($this->reflectionProperty->isProtected());
	}


	public function testIsPublic()
	{
		$this->assertTrue($this->reflectionProperty->isPublic());
	}


	public function testIsStatic()
	{
		$this->assertFalse($this->reflectionProperty->isStatic());
	}


	public function testGetDeclaringTrait()
	{
		$this->assertNull($this->reflectionProperty->getDeclaringTrait());
	}


	public function testGetDeclaringTraitName()
	{
		$this->assertNull($this->reflectionProperty->getDeclaringTraitName());
	}


	public function testIsValid()
	{
		$this->assertTrue($this->reflectionProperty->isValid());
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionFactory()
	{
		$parserResultMock = Mockery::mock(ParserResult::class);
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
		$configurationMock = Mockery::mock(Configuration::class);
		$configurationMock->shouldReceive('getOption')->with('php')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('deprecated')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('internal')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with(CO::VISIBILITY_LEVELS)->andReturn(256);
		return $configurationMock;
	}

}
