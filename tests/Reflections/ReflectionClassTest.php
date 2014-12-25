<?php

namespace ApiGen\Tests\Reflection;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use InvalidArgumentException;
use Mockery;
use PHPUnit_Framework_TestCase;
use TokenReflection\Broker;


class ReflectionClassTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ReflectionClass
	 */
	private $reflectionClass;

	/**
	 * @var ReflectionClass
	 */
	private $reflectionClassOfParent;


	protected function setUp()
	{
		$backend = new Backend($this->getReflectionFactory());
		$broker = new Broker($backend);
		$broker->processDirectory(__DIR__ . '/ReflectionClassSource');
		$this->reflectionClassOfParent = $backend->getClasses()['Project\ParentClass'];
		$this->reflectionClass = $backend->getClasses()['Project\AccessLevels'];
	}


	public function testInterface()
	{
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionClass', $this->reflectionClass);
	}


	public function testGetName()
	{
		$this->assertSame('Project\AccessLevels', $this->reflectionClass->getName());
	}


	public function testGetShortName()
	{
		$this->assertSame('AccessLevels', $this->reflectionClass->getShortName());
	}


	public function testIsAbstract()
	{
		$this->assertFalse($this->reflectionClass->isAbstract());
	}


	public function testIsFinal()
	{
		$this->assertFalse($this->reflectionClass->isFinal());
	}


	public function testIsInterface()
	{
		$this->assertFalse($this->reflectionClass->isInterface());
	}


	public function testIsSubclassOf()
	{
		$this->assertTrue($this->reflectionClass->isSubclassOf('Project\ParentClass'));
		$this->assertFalse($this->reflectionClass->isSubclassOf('ArrayAccess'));
	}


	public function testGetConstants()
	{
		$this->assertCount(1, $this->reflectionClass->getConstants());
	}


	public function testGetOwnConstants()
	{
		$this->assertCount(1, $this->reflectionClass->getOwnConstants());
	}


	public function testHasConstant()
	{
		$this->assertFalse($this->reflectionClass->hasConstant('NOT_EXISTING'));
		$this->assertTrue($this->reflectionClass->hasConstant('LEVEL'));
	}


	public function testGetConstant()
	{
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionConstant', $this->reflectionClass->getConstant('LEVEL'));
	}


	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testGetConstantNonExisting()
	{
		$this->reflectionClass->getConstant('NON_EXISTING');
	}


	public function testGetProperties()
	{
		$this->assertCount(3, $this->reflectionClass->getProperties());
	}


	public function testGetOwnProperties()
	{
		$this->assertCount(2, $this->reflectionClass->getOwnProperties());
	}


	public function testGetMagicProperties()
	{
		$this->assertCount(1, $this->reflectionClass->getMagicProperties());
	}


	public function testGetOwnMagicProperties()
	{
		$this->assertCount(1, $this->reflectionClass->getOwnMagicProperties());
	}


	public function testGetMethods()
	{
		$this->assertCount(3, $this->reflectionClass->getMethods());
	}


	public function testGetOwnMethods()
	{
		$this->assertCount(2, $this->reflectionClass->getOwnMethods());
	}


	public function testGetMagicMethods()
	{
		$this->assertCount(1, $this->reflectionClass->getMagicMethods());
	}


	public function testGetOwnMagicMethods()
	{
		$this->assertCount(1, $this->reflectionClass->getOwnMagicMethods());
	}


	public function testGetTraits()
	{
		$this->assertCount(0, $this->reflectionClass->getTraits());
	}


	public function testGetTraitNames()
	{
		$this->assertCount(0, $this->reflectionClass->getTraitNames());
	}


	public function testGetTraitAliases()
	{
		$this->assertCount(0, $this->reflectionClass->getTraitAliases());
	}


	public function testGetTraitMethods()
	{
		$this->assertCount(0, $this->reflectionClass->getTraitMethods());
	}


	public function testGetTraitProperties()
	{
		$this->assertCount(0, $this->reflectionClass->getTraitProperties());
	}


	public function testVisibility()
	{
		$this->assertTrue($this->reflectionClass->hasMethod('publicMethod'));
		$this->assertTrue($this->reflectionClass->hasMethod('protectedMethod'));
		$this->assertFalse($this->reflectionClass->hasMethod('privateMethod'));

		$this->assertTrue($this->reflectionClass->hasProperty('publicProperty'));
		$this->assertTrue($this->reflectionClass->hasProperty('protectedProperty'));
		$this->assertFalse($this->reflectionClass->hasProperty('privateProperty'));
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionFactory()
	{
		$parserResultMock = Mockery::mock('ApiGen\Parser\ParserResult');
		$parserResultMock->shouldReceive('getElementsByType')->andReturnUsing(function ($arg) {
			if ($arg) {
				return ['Project\ParentClass' => $this->reflectionClassOfParent];
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
		$configurationMock->shouldReceive('getOption')->with(CO::VISIBILITY_LEVELS)->andReturn(256 | 512);
		return $configurationMock;
	}

}
