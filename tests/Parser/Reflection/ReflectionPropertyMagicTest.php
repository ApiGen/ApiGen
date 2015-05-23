<?php

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionPropertyMagic;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Parser\Tests\Configuration\ParserConfiguration;
use Mockery;
use PHPUnit_Framework_TestCase;
use TokenReflection\Broker;


class ReflectionPropertyMagicTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ClassReflectionInterface
	 */
	private $reflectionClass;

	/**
	 * @var MagicPropertyReflectionInterface
	 */
	private $reflectionPropertyMagic;


	protected function setUp()
	{
		$backend = new Backend($this->getReflectionFactory());
		$broker = new Broker($backend);
		$broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

		$this->reflectionClass = $backend->getClasses()['Project\ReflectionMethod'];

		$this->reflectionPropertyMagic = $this->reflectionClass->getMagicProperties()['skillCounter'];
	}


	public function testInstance()
	{
		$this->assertInstanceOf(MagicPropertyReflectionInterface::class, $this->reflectionPropertyMagic);
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
		$this->assertInstanceOf(ClassReflectionInterface::class, $this->reflectionPropertyMagic->getDeclaringClass());
	}


	public function testGetDeclaringClassName()
	{
		$this->assertSame('Project\ReflectionMethod', $this->reflectionPropertyMagic->getDeclaringClassName());
	}


	public function testGetDefaultValue()
	{
		$this->assertNull($this->reflectionPropertyMagic->getDefaultValue());
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
