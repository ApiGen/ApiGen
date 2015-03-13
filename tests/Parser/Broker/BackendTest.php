<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Tests\Parser\Broker;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;
use TokenReflection\Broker;


class BackendTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var Backend
	 */
	private $backend;

	/**
	 * @var Broker
	 */
	private $broker;


	protected function setUp()
	{
		$this->backend = new Backend($this->getReflectionFactory());
		$this->broker = new Broker($this->backend);
	}


	public function testGetClasses()
	{
		$this->broker->processDirectory(__DIR__ . '/BackendSource');
		$classes = $this->backend->getClasses();
		$this->assertCount(1, $classes);

		$class = array_pop($classes);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionClass', $class);

		$this->checkLoadedProperties($class);
	}


	public function testGetFunctions()
	{
		$this->broker->processDirectory(__DIR__ . '/BackendSource');
		$functions = $this->backend->getFunctions();
		$this->assertCount(1, $functions);

		$function = array_pop($functions);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionFunction', $function);

		$this->checkLoadedProperties($function);
	}


	public function testGetConstants()
	{
		$this->broker->processDirectory(__DIR__ . '/BackendSource');
		$constants = $this->backend->getConstants();
		$this->assertCount(1, $constants);

		$constant = array_pop($constants);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionConstant', $constant);

		$this->checkLoadedProperties($constant);
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
		$configurationMock->shouldReceive('getOption')->with(CO::VISIBILITY_LEVELS)->andReturn(1);
		return $configurationMock;
	}

}
