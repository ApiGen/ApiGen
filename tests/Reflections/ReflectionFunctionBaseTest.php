<?php

namespace ApiGen\Tests\Reflection;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Reflection\ReflectionParameter;
use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Tests\MethodInvoker;
use InvalidArgumentException;
use Mockery;
use PHPUnit_Framework_TestCase;
use TokenReflection\Broker;


class ReflectionFunctionBaseTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ReflectionFunction
	 */
	private $reflectionFunction;

	/**
	 * @var ReflectionFunction
	 */
	private $reflectionFunctionWithMagicParameters;

	/**
	 * @var Backend
	 */
	private $backend;


	protected function setUp()
	{
		$this->backend = new Backend($this->getReflectionFactory());
		$broker = new Broker($this->backend);
		$broker->processDirectory(__DIR__ . '/ReflectionFunctionSource');
		$this->reflectionFunction = $this->backend->getFunctions()['getSomeData'];
	}


	public function testGetShortName()
	{
		$this->assertSame('getSomeData', $this->reflectionFunction->getShortName());
	}


	public function testReturnReference()
	{
		$this->assertFalse($this->reflectionFunction->returnsReference());
	}


	public function testGetParameters()
	{
		$parameters = $this->reflectionFunction->getParameters();
		$this->assertCount(1, $parameters);

		/** @var ReflectionParameter $parameter */
		$parameter = $parameters[0];
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionParameter', $parameter);
	}


	public function testGetParameter()
	{
		$parameter = $this->reflectionFunction->getParameter('arg');
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionParameter', $parameter);

		$parameter = $this->reflectionFunction->getParameter(0);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionParameter', $parameter);
	}


	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testGetParameterNotExistingName()
	{
		$this->reflectionFunction->getParameter('notHere');
	}


	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testGetParameterNotExistingPosition()
	{
		$this->reflectionFunction->getParameter(1);
	}


	public function testGetNumberOfParameters()
	{
		$this->assertSame(1, $this->reflectionFunction->getNumberOfParameters());
	}


	public function testGetNumberOfRequiredParameters()
	{
		$this->assertSame(1, $this->reflectionFunction->getNumberOfRequiredParameters());
	}


	public function testProcessAnnotation()
	{
		$reflectionFunction = $this->backend->getFunctions()['withMagicParameters'];
		$parameters = $reflectionFunction->getParameters();

		$this->assertCount(2, $parameters);
		$this->assertSame(2, $reflectionFunction->getNumberOfParameters());

		$this->assertInstanceOf('ApiGen\Reflection\ReflectionParameterMagic', $parameters[0]);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionParameterMagic', $parameters[1]);
	}


	public function testGetParametersAnnotationMatchingRealCount()
	{
		$reflectionFunction = $this->backend->getFunctions()['getMemoryInBytes'];

		/** @var ReflectionParameter[] $parameters */
		$parameters = $reflectionFunction->getParameters();
		$this->assertCount(1, $parameters);
		$this->assertSame(1, $reflectionFunction->getNumberOfParameters());

		$this->assertFalse($parameters[0]->isUnlimited());
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionFactory()
	{
		$parserResultMock = Mockery::mock('ApiGen\Parser\ParserResult');
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
		$configurationMock->shouldReceive('getOption')->with('skipDocPath')->andReturn([]);
		$configurationMock->shouldReceive('getOption')->with(CO::PROPERTY_AND_METHOD_ACCESS_LEVELS)->andReturn(256);
		return $configurationMock;
	}

}
