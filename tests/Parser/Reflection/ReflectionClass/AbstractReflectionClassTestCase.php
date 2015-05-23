<?php

namespace ApiGen\Parser\Tests\Reflection\ReflectionClass;

use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Parser\Tests\Configuration\ParserConfiguration;
use Mockery;
use PHPUnit_Framework_TestCase;
use ReflectionProperty;
use TokenReflection\Broker;


abstract class AbstractReflectionClassTestCase extends PHPUnit_Framework_TestCase
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
		$parserStorageMock = Mockery::mock(ParserStorageInterface::class);
		$parserStorageMock->shouldReceive('getDirectImplementersOfInterface')->andReturn([1]);
		$parserStorageMock->shouldReceive('getIndirectImplementersOfInterface')->andReturn([]);
		$parserStorageMock->shouldReceive('getElementsByType')->andReturnUsing(function ($arg) {
			if ($arg) {
				return [
					'Project\AccessLevels' => $this->reflectionClass,
					'Project\ParentClass' => $this->reflectionClassOfParent,
					'Project\SomeTrait' => $this->reflectionClassOfTrait,
					'Project\RichInterface' => $this->reflectionClassOfInterface
				];
			}
		});

		$parserConfiguration = new ParserConfiguration(
			ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED
		);

		return new ReflectionFactory($parserConfiguration, $parserStorageMock);
	}

}
