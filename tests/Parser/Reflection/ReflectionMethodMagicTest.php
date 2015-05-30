<?php

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionMethodMagic;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Parser\Tests\Configuration\ParserConfiguration;
use Mockery;
use PHPUnit_Framework_TestCase;
use ReflectionProperty;
use TokenReflection\Broker;


class ReflectionMethodMagicTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ReflectionMethodMagic
	 */
	private $reflectionMethodMagic;

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
		$this->reflectionMethodMagic = $this->reflectionClass->getMagicMethods()['getName'];
	}


	public function testInstance()
	{
		$this->assertInstanceOf(MagicMethodReflectionInterface::class, $this->reflectionMethodMagic);
	}


	public function testGetDeclaringClass()
	{
		$this->isInstanceOf(ClassReflectionInterface::class, $this->reflectionMethodMagic->getDeclaringClass());
	}


	public function testGetDeclaringClassName()
	{
		$this->assertSame('Project\ReflectionMethod', $this->reflectionMethodMagic->getDeclaringClassName());
	}


	public function testGetName()
	{
		$this->assertSame('getName', $this->reflectionMethodMagic->getName());
	}


	public function testGetShortDescription()
	{
		$this->assertSame('This is some short description.', $this->reflectionMethodMagic->getShortDescription());
	}


	public function testGetLongDescription()
	{
		$this->assertSame('This is some short description.', $this->reflectionMethodMagic->getLongDescription());
	}


	public function testReturnReference()
	{
		$this->assertFalse($this->reflectionMethodMagic->returnsReference());
	}


	public function testIsMagic()
	{
		$this->assertTrue($this->reflectionMethodMagic->isMagic());
	}


	public function testIsDocumented()
	{
		$this->assertTrue($this->reflectionMethodMagic->isDocumented());
	}


	public function testIsDeprecated()
	{
		$this->assertFalse($this->reflectionMethodMagic->isDeprecated());
	}


	public function testGetPackageName()
	{
		$this->assertSame('Some\Package', $this->reflectionMethodMagic->getPackageName());
	}


	public function testGetNamespaceName()
	{
		$this->assertSame('Project', $this->reflectionMethodMagic->getNamespaceName());
	}


	public function testGetAnnotations()
	{
		$this->assertSame(['return' => ['string']], $this->reflectionMethodMagic->getAnnotations());
	}


	/** methods of parent ReflectionMethod */


	public function testIsAbstract()
	{
		$this->assertFalse($this->reflectionMethodMagic->isAbstract());
	}


	public function testIsFinal()
	{
		$this->assertFalse($this->reflectionMethodMagic->isFinal());
	}


	public function testIsPrivate()
	{
		$this->assertFalse($this->reflectionMethodMagic->isPrivate());
	}


	public function testIsProtected()
	{
		$this->assertFalse($this->reflectionMethodMagic->isProtected());
	}


	public function testIsPublic()
	{
		$this->assertTrue($this->reflectionMethodMagic->isPublic());
	}


	public function testIsStatic()
	{
		$this->assertFalse($this->reflectionMethodMagic->isStatic());
	}


	public function testGetDeclaringTrait()
	{
		$this->assertNull($this->reflectionMethodMagic->getDeclaringTrait());
	}


	public function testGetDeclaringTraitName()
	{
		$this->assertNull($this->reflectionMethodMagic->getDeclaringTraitName());
	}


	public function testGetOriginalName()
	{
		$this->assertSame('getName', $this->reflectionMethodMagic->getOriginalName());
	}


	public function testIsValid()
	{
		$this->assertTrue($this->reflectionMethodMagic->isValid());
	}


	public function testStaticMethod()
	{
		$method = $this->reflectionClass->getMagicMethods()['doAStaticOperation'];
		$this->assertTrue($method->isStatic());
	}


	public function testStaticMethodReturnType()
	{
		$method = $this->reflectionClass->getMagicMethods()['doAStaticOperation'];
		$this->assertSame('string', current($method->getAnnotation('return')));
	}


	public function testVoidStaticMethod()
	{
		$method = $this->reflectionClass->getMagicMethods()['doAVoidStaticOperation'];
		$this->assertEmpty(current($method->getAnnotation('return')));
	}


	/**
	 * @return ReflectionFactoryInterface|ParserStorageInterface
	 */
	private function getReflectionFactory()
	{
		$parserStorageMock = Mockery::mock(ParserStorageInterface::class);
		$parserStorageMock->shouldReceive('getElementsByType')->andReturnUsing(function ($arg) {
			if ($arg) {
				return ['Project\ReflectionMethod' => $this->reflectionClass];
			}
		});

		$configurationMock = Mockery::mock(ConfigurationInterface::class, [
			'getVisibilityLevel' => ReflectionProperty::IS_PUBLIC,
			'isInternalDocumented' => FALSE,
			'isPhpCoreDocumented' => TRUE,
			'isDeprecatedDocumented' => FALSE
		]);
		return new ReflectionFactory($configurationMock, $parserStorageMock);
	}

}
