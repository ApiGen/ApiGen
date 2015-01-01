<?php

namespace ApiGen\Tests\Reflection;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionParameterMagic;
use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit_Framework_TestCase;
use TokenReflection\Broker;


class ReflectionParameterMagicTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ReflectionClass
	 */
	private $reflectionClass;

	/**
	 * @var ReflectionParameterMagic
	 */
	private $reflectionParameterMagic;


	protected function setUp()
	{
		$backend = new Backend($this->getReflectionFactory());
		$broker = new Broker($backend);
		$broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

		$this->reflectionClass = $backend->getClasses()['Project\ReflectionMethod'];
		$reflectionMethodMagic = $this->reflectionClass->getMagicMethods()['doAnOperation'];
		$this->reflectionParameterMagic = $reflectionMethodMagic->getParameters()['data'];
	}


	public function testInstance()
	{
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionParameterMagic', $this->reflectionParameterMagic);
	}


	public function testGetName()
	{
		$this->assertSame('data', $this->reflectionParameterMagic->getName());
	}


	public function testGetTypeHint()
	{
		$this->assertSame('\stdClass', $this->reflectionParameterMagic->getTypeHint());
	}


	public function testGetFileName()
	{
		$this->assertStringEndsWith('ReflectionMethod.php', $this->reflectionParameterMagic->getFileName());
	}


	public function testIsTokenized()
	{
		$this->assertTrue($this->reflectionParameterMagic->isTokenized());
	}


	public function testGetPrettyName()
	{
		$this->assertSame('Project\ReflectionMethod::doAnOperation($data)', $this->reflectionParameterMagic->getPrettyName());
	}


	public function testGetDeclaringClass()
	{
		$this->assertInstanceOf(
			'ApiGen\Reflection\ReflectionClass', $this->reflectionParameterMagic->getDeclaringClass()
		);
	}


	public function testGetDeclaringClassName()
	{
		$this->assertSame('Project\ReflectionMethod', $this->reflectionParameterMagic->getDeclaringClassName());
	}


	public function testGetDeclaringFunction()
	{
		$this->assertInstanceOf(
			'ApiGen\Reflection\ReflectionMethodMagic', $this->reflectionParameterMagic->getDeclaringFunction()
		);
	}


	public function testGetDeclaringFunctionName()
	{
		$this->assertSame('doAnOperation', $this->reflectionParameterMagic->getDeclaringFunctionName());
	}


	public function testStartLine()
	{
		$this->assertSame(17, $this->reflectionParameterMagic->getStartLine());
	}


	public function testEndLine()
	{
		$this->assertSame(17, $this->reflectionParameterMagic->getEndLine());
	}


	public function testGetDocComment()
	{
		$this->assertSame('', $this->reflectionParameterMagic->getDocComment());
	}


	public function testGetDefaultValueDefinition()
	{
		$this->assertSame('', $this->reflectionParameterMagic->getDefaultValueDefinition());
	}


	public function testIsDefaultValueAvailable()
	{
		$this->assertFalse($this->reflectionParameterMagic->isDefaultValueAvailable());
	}


	public function testGetPosition()
	{
		$this->assertSame(0, $this->reflectionParameterMagic->getPosition());
	}


	public function testIsArray()
	{
		$this->assertFalse($this->reflectionParameterMagic->isArray());
	}


	public function testIsCallable()
	{
		$this->assertFalse($this->reflectionParameterMagic->isCallable());
	}


	public function testGetClass()
	{
		$this->assertNull($this->reflectionParameterMagic->getClass());
	}


	public function testGetClassName()
	{
		$this->assertNull($this->reflectionParameterMagic->getClassName());
	}


	public function testAllowsNull()
	{
		$this->assertFalse($this->reflectionParameterMagic->allowsNull());
	}


	public function testIsOptional()
	{
		$this->assertFalse($this->reflectionParameterMagic->isOptional());
	}


	public function testIsPassedByReference()
	{
		$this->assertFalse($this->reflectionParameterMagic->isPassedByReference());
	}


	public function testCanBePassedByValue()
	{
		$this->assertFalse($this->reflectionParameterMagic->canBePassedByValue());
	}


	public function testIsUnlimited()
	{
		$this->assertFalse($this->reflectionParameterMagic->isUnlimited());
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionFactory()
	{
		$parserResultMock = Mockery::mock('ApiGen\Parser\ParserResult');
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
		$configurationMock = Mockery::mock('ApiGen\Configuration\Configuration');
		$configurationMock->shouldReceive('getOption')->with('php')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('deprecated')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('internal')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('skipDocPath')->andReturn(['*SomeConstant.php*']);
		$configurationMock->shouldReceive('getOption')->with(CO::VISIBILITY_LEVELS)->andReturn(256);
		return $configurationMock;
	}

}
