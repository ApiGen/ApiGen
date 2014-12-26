<?php

namespace ApiGen\Tests\Reflections\ReflectionClass;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit_Framework_TestCase;
use TokenReflection\Broker;


abstract class TestCase extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ReflectionClass
	 */
	protected $reflectionClass;

	/**
	 * @var ReflectionClass
	 */
	protected $reflectionClassOfParent;

	/**
	 * @var ReflectionClass
	 */
	protected $reflectionClassOfTrait;


	protected function setUp()
	{
		$backend = new Backend($this->getReflectionFactory());
		$broker = new Broker($backend);
		$broker->processDirectory(__DIR__ . '/ReflectionClassSource');
		$this->reflectionClass = $backend->getClasses()['Project\AccessLevels'];
		$this->reflectionClassOfParent = $backend->getClasses()['Project\ParentClass'];
		$this->reflectionClassOfTrait = $backend->getClasses()['Project\SomeTrait'];
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionFactory()
	{
		$parserResultMock = Mockery::mock('ApiGen\Parser\ParserResult');
		$parserResultMock->shouldReceive('getElementsByType')->andReturnUsing(function ($arg) {
			if ($arg) {
				return [
					'Project\ParentClass' => $this->reflectionClassOfParent,
					'Project\SomeTrait' => $this->reflectionClassOfTrait
				];
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
