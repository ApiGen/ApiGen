<?php

namespace ApiGen\Tests\Console;

use ApiGen\Console\Application;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Input\InputDefinition;


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
		$inputMock = Mockery::mock('Symfony\Component\Console\Input\InputInterface');
		$inputMock->shouldReceive('hasParameterOption');
		$inputMock->shouldReceive('getFirstArgument');

		$outputMock = Mockery::mock('Symfony\Component\Console\Output\OutputInterface');
		$outputMock->shouldReceive('write');
		$eventManagerMock = Mockery::mock('Kdyby\Events\EventManager');
		$eventManagerMock->shouldReceive('dispatchEvent');

		$this->application->setEventManager($eventManagerMock);
		$this->application->doRun($inputMock, $outputMock);
	}


	public function testGetLongVersion()
	{
		$this->assertSame(
			'<info>ApiGen</info> version <comment>@package_version@</comment> @release_date@',
			$this->application->getLongVersion()
		);
	}


	public function testGetDefaultInputDefinition()
	{
		/** @var InputDefinition $defaultInputDefinition */
		$defaultInputDefinition = MethodInvoker::callMethodOnObject($this->application, 'getDefaultInputDefinition');
		$this->assertInstanceOf('Symfony\Component\Console\Input\InputDefinition', $defaultInputDefinition);
		$this->assertSame(1, $defaultInputDefinition->getArgumentCount());
		$this->assertCount(3, $defaultInputDefinition->getOptions());
	}

}
