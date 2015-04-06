<?php

namespace ApiGen\Tests\Reflections\ReflectionClass;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\ParserResult;
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

	/**
	 * @var ReflectionClass
	 */
	protected $reflectionClassOfInterface;


	protected function setUp()
	{
		$backend = new Backend($this->getReflectionFactory());
		$broker = new Broker($backend);
		$broker->processDirectory(__DIR__ . '/../ReflectionClassSource');
		$this->reflectionClass = $backend->getClasses()['Project\AccessLevels'];
		$this->reflectionClassOfParent = $backend->getClasses()['Project\ParentClass'];
		$this->reflectionClassOfTrait = $backend->getClasses()['Project\SomeTrait'];
		$this->reflectionClassOfInterface = $backend->getClasses()['Project\RichInterface'];
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionFactory()
	{
		$parserResultMock = Mockery::mock(ParserResult::class);
		$parserResultMock->shouldReceive('getDirectImplementersOfInterface')->andReturn([1]);
		$parserResultMock->shouldReceive('getIndirectImplementersOfInterface')->andReturn([]);
		$parserResultMock->shouldReceive('getElementsByType')->andReturnUsing(function ($arg) {
			if ($arg) {
				return [
					'Project\AccessLevels' => $this->reflectionClass,
					'Project\ParentClass' => $this->reflectionClassOfParent,
					'Project\SomeTrait' => $this->reflectionClassOfTrait,
					'Project\RichInterface' => $this->reflectionClassOfInterface
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
		$configurationMock = Mockery::mock(Configuration::class);
		$configurationMock->shouldReceive('getOption')->with('php')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('deprecated')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('internal')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with(CO::VISIBILITY_LEVELS)->andReturn(256 | 512);
		return $configurationMock;
	}

}
