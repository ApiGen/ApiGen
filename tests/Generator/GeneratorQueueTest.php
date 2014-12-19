<?php

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\GeneratorQueue;
use Mockery;
use PHPUnit_Framework_TestCase;
use ReflectionClass;


class GeneratorQueueTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var GeneratorQueue
	 */
	private $generatorQueue;


	protected function setUp()
	{
		$progressBarMock = Mockery::mock('ApiGen\Console\ProgressBar');
		$progressBarMock->shouldReceive('init');
		$this->generatorQueue = new GeneratorQueue($progressBarMock);
	}


	public function testAddToQueueAndGetQueue()
	{
		$templateGeneratorMock = Mockery::mock('ApiGen\Generator\TemplateGenerator');
		$this->generatorQueue->addToQueue($templateGeneratorMock);
		$this->assertCount(1, $this->generatorQueue->getQueue());
	}


	public function testGetStepCount()
	{
		$templateGeneratorMock = Mockery::mock('ApiGen\Generator\TemplateGenerator', 'ApiGen\Generator\StepCounter');
		$templateGeneratorMock->shouldReceive('getStepCount')->andReturn(50);
		$this->generatorQueue->addToQueue($templateGeneratorMock);
		$this->assertSame(50, $this->invokePrivateMethodOnObject($this->generatorQueue, 'getStepCount'));
	}


	public function testGetAllowedQueue()
	{
		$templateGeneratorMock = Mockery::mock('ApiGen\Generator\ConditionalTemplateGenerator');
		$templateGeneratorMock->shouldReceive('isAllowed')->andReturn(TRUE);
		$this->generatorQueue->addToQueue($templateGeneratorMock);
		$templateGeneratorMockNotAllowed = Mockery::mock('ApiGen\Generator\ConditionalTemplateGenerator');
		$templateGeneratorMockNotAllowed->shouldReceive('isAllowed')->andReturn(FALSE);
		$this->generatorQueue->addToQueue($templateGeneratorMockNotAllowed);

		$allowedQueue = $this->invokePrivateMethodOnObject($this->generatorQueue, 'getAllowedQueue');
		$this->assertCount(1, $allowedQueue);
	}


	/**
	 * @param object $object
	 * @param string $method
	 * @return mixed
	 */
	private function invokePrivateMethodOnObject($object, $method)
	{
		$objectReflection = new ReflectionClass($object);
		$methodReflection = $objectReflection->getMethod($method);
		$methodReflection->setAccessible(TRUE);
		return $methodReflection->invokeArgs($object, []);
	}

}
