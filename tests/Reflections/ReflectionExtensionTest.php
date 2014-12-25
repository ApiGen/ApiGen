<?php

namespace ApiGen\Tests\Reflection;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionExtension;
use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit_Framework_TestCase;
use TokenReflection\Broker;


class ReflectionExtensionTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ReflectionExtension
	 */
	private $reflectionExtension;


	protected function setUp()
	{
		$backend = new Backend($this->getReflectionFactory());
		$broker = new Broker($backend);
		$broker->processDirectory(__DIR__ . '/ReflectionExtensionSource');

		/** @var ReflectionClass $reflectionClass */
		$reflectionClass = $broker->getClasses(Backend::INTERNAL_CLASSES)['Countable'];
		$this->reflectionExtension = $reflectionClass->getExtension();
	}


	public function testGetName()
	{
		$this->assertSame('SPL', $this->reflectionExtension->getName());
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
		$configurationMock->shouldReceive('getOption')->with('php')->andReturn(TRUE);
		$configurationMock->shouldReceive('getOption')->with('deprecated')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('internal')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('skipDocPath')->andReturn([]);
		$configurationMock->shouldReceive('getOption')->with(CO::PROPERTY_AND_METHOD_ACCESS_LEVELS)->andReturn(256);
		return $configurationMock;
	}

}
