<?php

namespace ApiGen\Tests\Console;

use ApiGen\ApiGen;
use ApiGen\Console\Application;
use ApiGen\Tests\MethodInvoker;
use Kdyby\Events\EventManager;
use Mockery;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ApplicationTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var Application
	 */
	private $application;


	protected function setUp()
	{
		$this->application = new Application;
	}


	public function testDoRun()
	{
		$inputMock = Mockery::mock(InputInterface::class);
		$inputMock->shouldReceive('hasParameterOption');
		$inputMock->shouldReceive('getFirstArgument');

		$outputMock = Mockery::mock(OutputInterface::class);
		$outputMock->shouldReceive('write');
		$eventManagerMock = Mockery::mock(EventManager::class);
		$eventManagerMock->shouldReceive('dispatchEvent');

		$this->application->setEventManager($eventManagerMock);
		$this->application->doRun($inputMock, $outputMock);
	}


	public function testGetLongVersion()
	{
		$this->assertSame(
			'<info>ApiGen</info> version <comment>' . ApiGen::VERSION . '</comment>',
			$this->application->getLongVersion()
		);
	}


	public function testGetDefaultInputDefinition()
	{
		/** @var InputDefinition $defaultInputDefinition */
		$defaultInputDefinition = MethodInvoker::callMethodOnObject($this->application, 'getDefaultInputDefinition');
		$this->assertInstanceOf(InputDefinition::class, $defaultInputDefinition);
		$this->assertSame(1, $defaultInputDefinition->getArgumentCount());
		$this->assertCount(3, $defaultInputDefinition->getOptions());
	}

}
