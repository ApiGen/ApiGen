<?php

namespace ApiGen\Tests\Reflection;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\ParserResult;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionPropertyMagic;
use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit_Framework_TestCase;
use Project;
use TokenReflection\Broker;


class ReflectionPropertyMagicTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ReflectionClass
	 */
	private $reflectionClass;

	/**
	 * @var ReflectionPropertyMagic
	 */
	private $reflectionPropertyMagic;


	protected function setUp()
	{
		$backend = new Backend($this->getReflectionFactory());
		$broker = new Broker($backend);
		$broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

		$this->reflectionClass = $backend->getClasses()[Project\ReflectionMethod::class];

		$this->reflectionPropertyMagic = $this->reflectionClass->getMagicProperties()['skillCounter'];
	}


	public function testInstance()
	{
		$this->assertInstanceOf(ReflectionPropertyMagic::class, $this->reflectionPropertyMagic);
	}


	public function testIsReadOnly()
	{
		$this->assertTrue($this->reflectionPropertyMagic->isReadOnly());
	}


	public function testIsWriteOnly()
	{
		$this->assertFalse($this->reflectionPropertyMagic->isWriteOnly());
	}


	public function testIsMagic()
	{
		$this->assertTrue($this->reflectionPropertyMagic->isMagic());
	}


	public function testGetTypeHint()
	{
		$this->assertSame('int', $this->reflectionPropertyMagic->getTypeHint());
	}


	public function testGetDeclaringClass()
	{
		$this->assertInstanceOf(ReflectionClass::class, $this->reflectionPropertyMagic->getDeclaringClass());
	}


	public function testGetDeclaringClassName()
	{
		$this->assertSame(Project\ReflectionMethod::class, $this->reflectionPropertyMagic->getDeclaringClassName());
	}


	public function testGetDefaultValue()
	{
		$this->assertNull($this->reflectionPropertyMagic->getDefaultValue());
	}


	public function testGetDefaultValueDefinition()
	{
		$this->assertSame('', $this->reflectionPropertyMagic->getDefaultValueDefinition());
	}


	public function testIsDefault()
	{
		$this->assertFalse($this->reflectionPropertyMagic->isDefault());
	}


	public function testIsPrivate()
	{
		$this->assertFalse($this->reflectionPropertyMagic->isPrivate());
	}


	public function testIsProtected()
	{
		$this->assertFalse($this->reflectionPropertyMagic->isProtected());
	}


	public function testIsPublic()
	{
		$this->assertTrue($this->reflectionPropertyMagic->isPublic());
	}


	public function testIsStatic()
	{
		$this->assertFalse($this->reflectionPropertyMagic->isStatic());
	}


	public function testGetDeclaringTrait()
	{
		$this->assertNull($this->reflectionPropertyMagic->getDeclaringTrait());
	}


	public function testGetDeclaringTraitName()
	{
		$this->assertNull($this->reflectionPropertyMagic->getDeclaringTraitName());
	}


	public function testIsValid()
	{
		$this->assertTrue($this->reflectionPropertyMagic->isValid());
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
