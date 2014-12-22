<?php

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\GeneratorQueue;

use ApiGen\Tests\MethodInvoker;
use Mockery;
use PHPUnit_Framework_TestCase;


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


	public function testRun()
	{
		$templateGeneratorMock = Mockery::mock('ApiGen\Generator\TemplateGenerator');
		$templateGeneratorMock->shouldReceive('generate')->andReturn(file_put_contents(TEMP_DIR . '/file.txt', '...'));
		$this->generatorQueue->addToQueue($templateGeneratorMock);
		$this->generatorQueue->run();

		$this->assertFileExists(TEMP_DIR . '/file.txt');
	}


	public function testAddToQueueAndGetQueue()
	{
		$templateGeneratorMock = Mockery::mock('ApiGen\Generator\TemplateGenerator');
		$this->generatorQueue->addToQueue($templateGeneratorMock);
		$this->assertCount(1, $this->generatorQueue->getQueue());
	}


	public function testGetAllowedQueue()
	{
		$templateGeneratorMock = Mockery::mock('ApiGen\Generator\TemplateGenerator');
		$this->generatorQueue->addToQueue($templateGeneratorMock);

		$templateGeneratorConditionalMock = Mockery::mock('ApiGen\Generator\ConditionalTemplateGenerator');
		$templateGeneratorConditionalMock->shouldReceive('isAllowed')->andReturn(FALSE);
		$this->generatorQueue->addToQueue($templateGeneratorConditionalMock);

		$this->assertCount(1, MethodInvoker::callMethodOnObject($this->generatorQueue, 'getAllowedQueue'));
	}


	public function testGetStepCount()
	{
		$templateGeneratorMock = Mockery::mock('ApiGen\Generator\TemplateGenerator', 'ApiGen\Generator\StepCounter');
		$templateGeneratorMock->shouldReceive('getStepCount')->andReturn(50);
		$this->generatorQueue->addToQueue($templateGeneratorMock);

		$this->assertSame(50, MethodInvoker::callMethodOnObject($this->generatorQueue, 'getStepCount'));
	}

}
