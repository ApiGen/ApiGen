<?php

namespace ApiGen\Tests\Reflection\TokenReflection;

use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;


class ReflectionFactoryTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ReflectionFactory
	 */
	private $reflectionFactory;


	protected function setUp()
	{
		$configurationMock = Mockery::mock('ApiGen\Configuration\Configuration');
		$parserResultMock = Mockery::mock('ApiGen\Parser\ParserResult');
		$this->reflectionFactory = new ReflectionFactory($configurationMock, $parserResultMock);
	}


	public function testCreateMethodMagic()
	{
		$methodMagic = $this->reflectionFactory->createMethodMagic();
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionMethodMagic', $methodMagic);
		$this->checkLoadedProperties($methodMagic);
	}


	public function testCreateParameterMagic()
	{
		$parameterMagic = $this->reflectionFactory->createParameterMagic([
			'name' => '', 'position' => '', 'typeHint' => '', 'defaultValueDefinition' => '',
			'unlimited' => '', 'passedByReference' => '', 'declaringFunction' => ''
		]);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionParameterMagic', $parameterMagic);
		$this->checkLoadedProperties($parameterMagic);
	}


	public function testCreateFromReflectionClass()
	{
		$tokenReflectionClassMock = Mockery::mock('TokenReflection\IReflectionClass', 'Nette\Object');
		$reflectionClass = $this->reflectionFactory->createFromReflection($tokenReflectionClassMock);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionClass', $reflectionClass);
		$this->checkLoadedProperties($reflectionClass);
	}


	public function testCreateFromReflectionFunction()
	{
		$tokenReflectionFunctionMock = Mockery::mock('TokenReflection\IReflectionFunction', 'Nette\Object');
		$reflectionFunction = $this->reflectionFactory->createFromReflection($tokenReflectionFunctionMock);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionFunction', $reflectionFunction);
		$this->checkLoadedProperties($reflectionFunction);
	}


	public function testCreateFromReflectionMethod()
	{
		$tokenReflectionMethodMock = Mockery::mock('TokenReflection\IReflectionMethod', 'Nette\Object');
		$reflectionMethod = $this->reflectionFactory->createFromReflection($tokenReflectionMethodMock);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionMethod', $reflectionMethod);
		$this->checkLoadedProperties($reflectionMethod);
	}


	public function testCreateFromReflectionProperty()
	{
		$tokenReflectionPropertyMock = Mockery::mock('TokenReflection\IReflectionProperty', 'Nette\Object');
		$reflectionProperty = $this->reflectionFactory->createFromReflection($tokenReflectionPropertyMock);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionProperty', $reflectionProperty);
		$this->checkLoadedProperties($reflectionProperty);
	}


	public function testCreateFromReflectionParameter()
	{
		$tokenReflectionParameterMock = Mockery::mock('TokenReflection\IReflectionParameter', 'Nette\Object');
		$reflectionParameter = $this->reflectionFactory->createFromReflection($tokenReflectionParameterMock);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionParameter', $reflectionParameter);
		$this->checkLoadedProperties($reflectionParameter);
	}


	public function testCreateFromReflectionConstant()
	{
		$tokenReflectionConstantMock = Mockery::mock('TokenReflection\IReflectionConstant', 'Nette\Object');
		$reflectionConstant = $this->reflectionFactory->createFromReflection($tokenReflectionConstantMock);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionConstant', $reflectionConstant);
		$this->checkLoadedProperties($reflectionConstant);
	}


	public function testCreateFromReflectionExtension()
	{
		$tokenReflectionExtensionMock = Mockery::mock('TokenReflection\IReflectionExtension', 'Nette\Object');
		$reflectionExtension = $this->reflectionFactory->createFromReflection($tokenReflectionExtensionMock);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionExtension', $reflectionExtension);
		$this->checkLoadedProperties($reflectionExtension);
	}


	private function checkLoadedProperties($object)
	{
		$this->assertInstanceOf(
			'ApiGen\Configuration\Configuration',
			PHPUnit_Framework_Assert::getObjectAttribute($object, 'configuration')
		);

		$this->assertInstanceOf(
			'ApiGen\Parser\ParserResult',
			PHPUnit_Framework_Assert::getObjectAttribute($object, 'parserResult')
		);

		$this->assertInstanceOf(
			'ApiGen\Reflection\TokenReflection\ReflectionFactory',
			PHPUnit_Framework_Assert::getObjectAttribute($object, 'reflectionFactory')
		);
	}

}
