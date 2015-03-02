<?php

namespace ApiGen\Tests\Generator;

use ApiGen\Console\ProgressBar;
use ApiGen\Generator\ConditionalTemplateGenerator;
use ApiGen\Generator\GeneratorQueue;
use ApiGen\Generator\StepCounter;
use ApiGen\Generator\TemplateGenerator;
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
		$progressBarMock = Mockery::mock(ProgressBar::class);
		$progressBarMock->shouldReceive('init');
		$this->generatorQueue = new GeneratorQueue($progressBarMock);
	}


	public function testRun()
	{
		$templateGeneratorMock = Mockery::mock(TemplateGenerator::class);
		$templateGeneratorMock->shouldReceive('generate')->andReturn(file_put_contents(TEMP_DIR . '/file.txt', '...'));
		$this->generatorQueue->addToQueue($templateGeneratorMock);
		$this->generatorQueue->run();

		$this->assertFileExists(TEMP_DIR . '/file.txt');
	}


	public function testAddToQueueAndGetQueue()
	{
		$templateGeneratorMock = Mockery::mock(TemplateGenerator::class);
		$this->generatorQueue->addToQueue($templateGeneratorMock);
		$this->assertCount(1, $this->generatorQueue->getQueue());
	}


	public function testGetAllowedQueue()
	{
		$templateGeneratorMock = Mockery::mock(TemplateGenerator::class);
		$this->generatorQueue->addToQueue($templateGeneratorMock);

		$templateGeneratorConditionalMock = Mockery::mock(ConditionalTemplateGenerator::class);
		$templateGeneratorConditionalMock->shouldReceive('isAllowed')->andReturn(FALSE);
		$this->generatorQueue->addToQueue($templateGeneratorConditionalMock);

		$this->assertCount(1, MethodInvoker::callMethodOnObject($this->generatorQueue, 'getAllowedQueue'));
	}


	public function testGetStepCount()
	{
		$templateGeneratorMock = Mockery::mock(TemplateGenerator::class, StepCounter::class);
		$templateGeneratorMock->shouldReceive('getStepCount')->andReturn(50);
		$this->generatorQueue->addToQueue($templateGeneratorMock);

		$this->assertSame(50, MethodInvoker::callMethodOnObject($this->generatorQueue, 'getStepCount'));
	}

}
