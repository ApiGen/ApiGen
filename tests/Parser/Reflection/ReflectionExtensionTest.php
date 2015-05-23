<?php

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\Configuration\ParserConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionExtension;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Parser\Tests\Configuration\ParserConfiguration;
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
	 * @return ReflectionFactoryInterface
	 */
	private function getReflectionFactory()
	{
		$parserStorageMock = Mockery::mock(ParserStorageInterface::class);
		$parserConfiguration = Mockery::mock(ParserConfigurationInterface::class);
		$parserConfiguration->shouldReceive('getVisibilityLevel')->andReturn(1);
		$parserConfiguration->shouldReceive('isPhpCoreDocumented')->andReturn(TRUE);
		$parserConfiguration->shouldReceive('isInternalDocumented')->andReturn(FALSE);
		return new ReflectionFactory($parserConfiguration, $parserStorageMock);
	}

}
