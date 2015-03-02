<?php

namespace ApiGen\Tests\Reflection;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\ParserResult;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionMethodMagic;
use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit_Framework_TestCase;
use Project\ReflectionMethod;
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
		$this->assertInstanceOf(ReflectionMethodMagic::class, $this->reflectionMethodMagic);
	}


	public function testGetDeclaringClass()
	{
		$this->isInstanceOf(ReflectionClass::class, $this->reflectionMethodMagic->getDeclaringClass());
	}


	public function testGetDeclaringClassName()
	{
		$this->assertSame(ReflectionMethod::class, $this->reflectionMethodMagic->getDeclaringClassName());
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


	public function testIsConstructor()
	{
		$this->assertFalse($this->reflectionMethodMagic->isConstructor());
	}


	public function testIsDestructor()
	{
		$this->assertFalse($this->reflectionMethodMagic->isDestructor());
	}


	public function testGetDeclaringTrait()
	{
		$this->assertNull($this->reflectionMethodMagic->getDeclaringTrait());
	}


	public function testGetDeclaringTraitName()
	{
		$this->assertNull($this->reflectionMethodMagic->getDeclaringTraitName());
	}


	public function testGetOriginal()
	{
		$this->assertNull($this->reflectionMethodMagic->getOriginal());
	}


	public function testGetOriginalName()
	{
		$this->assertSame('getName', $this->reflectionMethodMagic->getOriginalName());
	}


	public function testIsValid()
	{
		$this->assertTrue($this->reflectionMethodMagic->isValid());
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
		$configurationMock->shouldReceive('getOption')->with('skipDocPath')->andReturn(['*SomeConstant.php*']);
		$configurationMock->shouldReceive('getOption')->with(CO::VISIBILITY_LEVELS)->andReturn(256);
		return $configurationMock;
	}

}
