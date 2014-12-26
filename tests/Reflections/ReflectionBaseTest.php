<?php

namespace ApiGen\Tests\Reflection;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use PHPUnit_Framework_TestCase;
use TokenReflection\Broker;


class ReflectionBaseTest extends PHPUnit_Framework_TestCase
{

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
	}


	public function testGetName()
	{
		$this->assertSame('Project\ReflectionMethod', $this->reflectionClass->getName());
	}


	public function testGetPrettyName()
	{
		$this->assertSame('Project\ReflectionMethod', $this->reflectionClass->getPrettyName());
	}


	public function testIsInternal()
	{
		$this->assertFalse($this->reflectionClass->isInternal());
	}


	public function testIsTokenized()
	{
		$this->assertTrue($this->reflectionClass->isTokenized());
	}


	public function testGetFileName()
	{
		$this->assertStringEndsWith('ReflectionMethodSource/ReflectionMethod.php', $this->reflectionClass->getFileName());
	}


	public function testGetStartLine()
	{
		$this->assertSame(6, $this->reflectionClass->getStartLine());
	}


	public function testGetEndLine()
	{
		$this->assertSame(33, $this->reflectionClass->getEndLine());
	}


	public function testGetParsedClasses()
	{
		$parsedClasses = MethodInvoker::callMethodOnObject($this->reflectionClass, 'getParsedClasses');
		$this->assertCount(1, $parsedClasses);
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionFactory()
	{
		$parserResultMock = Mockery::mock('ApiGen\Parser\ParserResult');
		$parserResultMock->shouldReceive('getElementsByType')->andReturn(['...']);
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
