<?php

namespace ApiGen\Parser\Tests\Reflection\TokenReflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ExtensionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicParameterReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use Nette\Object;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;
use TokenReflection\IReflectionClass;
use TokenReflection\IReflectionConstant;
use TokenReflection\IReflectionExtension;
use TokenReflection\IReflectionFunction;
use TokenReflection\IReflectionMethod;
use TokenReflection\IReflectionParameter;
use TokenReflection\IReflectionProperty;


class ReflectionFactoryTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ReflectionFactoryInterface
	 */
	private $reflectionFactory;


	protected function setUp()
	{
		$parserStorageMock = Mockery::mock(ParserStorageInterface::class);
		$configurationMock = Mockery::mock(ConfigurationInterface::class);
		$this->reflectionFactory = new ReflectionFactory($configurationMock, $parserStorageMock);
	}


	public function testCreateMethodMagic()
	{
		$methodMagic = $this->reflectionFactory->createMethodMagic([
			'name' => '', 'shortDescription' => '', 'startLine' => '', 'endLine' => '', 'returnsReference' => '',
			'declaringClass' => '', 'annotations' => ''
		]);
		$this->assertInstanceOf(MagicMethodReflectionInterface::class, $methodMagic);
		$this->checkLoadedProperties($methodMagic);
	}


	public function testCreateParameterMagic()
	{
		$parameterMagic = $this->reflectionFactory->createParameterMagic([
			'name' => '', 'position' => '', 'typeHint' => '', 'defaultValueDefinition' => '',
			'unlimited' => '', 'passedByReference' => '', 'declaringFunction' => ''
		]);
		$this->assertInstanceOf(MagicParameterReflectionInterface::class, $parameterMagic);
		$this->checkLoadedProperties($parameterMagic);
	}


	public function testCreateFromReflectionClass()
	{
		$tokenReflectionClassMock = Mockery::mock(IReflectionClass::class, Object::class);
		$reflectionClass = $this->reflectionFactory->createFromReflection($tokenReflectionClassMock);
		$this->assertInstanceOf(ClassReflectionInterface::class, $reflectionClass);
		$this->checkLoadedProperties($reflectionClass);
	}


	public function testCreatePropertyMagic()
	{
		$propertyMagic = $this->reflectionFactory->createPropertyMagic([
			'name' => '', 'typeHint' => '', 'shortDescription' => '', 'startLine' => '',
			'endLine' => '', 'readOnly' => '', 'writeOnly' => '', 'declaringClass' => ''
		]);
		$this->assertInstanceOf(MagicPropertyReflectionInterface::class, $propertyMagic);
		$this->checkLoadedProperties($propertyMagic);
	}


	public function testCreateFromReflectionFunction()
	{
		$tokenReflectionFunctionMock = Mockery::mock(IReflectionFunction::class, Object::class);
		$reflectionFunction = $this->reflectionFactory->createFromReflection($tokenReflectionFunctionMock);
		$this->assertInstanceOf(FunctionReflectionInterface::class, $reflectionFunction);
		$this->checkLoadedProperties($reflectionFunction);
	}


	public function testCreateFromReflectionMethod()
	{
		$tokenReflectionMethodMock = Mockery::mock(IReflectionMethod::class, Object::class);
		$reflectionMethod = $this->reflectionFactory->createFromReflection($tokenReflectionMethodMock);
		$this->assertInstanceOf(MethodReflectionInterface::class, $reflectionMethod);
		$this->checkLoadedProperties($reflectionMethod);
	}


	public function testCreateFromReflectionProperty()
	{
		$tokenReflectionPropertyMock = Mockery::mock(IReflectionProperty::class, Object::class);
		$reflectionProperty = $this->reflectionFactory->createFromReflection($tokenReflectionPropertyMock);
		$this->assertInstanceOf(PropertyReflectionInterface::class, $reflectionProperty);
		$this->checkLoadedProperties($reflectionProperty);
	}


	public function testCreateFromReflectionParameter()
	{
		$tokenReflectionParameterMock = Mockery::mock(IReflectionParameter::class, Object::class);
		$reflectionParameter = $this->reflectionFactory->createFromReflection($tokenReflectionParameterMock);
		$this->assertInstanceOf(ParameterReflectionInterface::class, $reflectionParameter);
		$this->checkLoadedProperties($reflectionParameter);
	}


	public function testCreateFromReflectionConstant()
	{
		$tokenReflectionConstantMock = Mockery::mock(IReflectionConstant::class, Object::class);
		$reflectionConstant = $this->reflectionFactory->createFromReflection($tokenReflectionConstantMock);
		$this->assertInstanceOf(ConstantReflectionInterface::class, $reflectionConstant);
		$this->checkLoadedProperties($reflectionConstant);
	}


	public function testCreateFromReflectionExtension()
	{
		$tokenReflectionExtensionMock = Mockery::mock(IReflectionExtension::class, Object::class);
		$reflectionExtension = $this->reflectionFactory->createFromReflection($tokenReflectionExtensionMock);
		$this->assertInstanceOf(ExtensionReflectionInterface::class, $reflectionExtension);
		$this->checkLoadedProperties($reflectionExtension);
	}


	private function checkLoadedProperties($object)
	{
		$this->assertInstanceOf(
			ConfigurationInterface::class,
			PHPUnit_Framework_Assert::getObjectAttribute($object, 'configuration')
		);

		$this->assertInstanceOf(
			ParserStorageInterface::class,
			PHPUnit_Framework_Assert::getObjectAttribute($object, 'parserResult')
		);

		$this->assertInstanceOf(
			ReflectionFactoryInterface::class,
			PHPUnit_Framework_Assert::getObjectAttribute($object, 'reflectionFactory')
		);
	}

}
