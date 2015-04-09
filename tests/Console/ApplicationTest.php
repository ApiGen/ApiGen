<?php

namespace ApiGen\Tests\Console;

use ApiGen\ApiGen;
use ApiGen\Console\Application;
use ApiGen\Contracts\EventDispatcher\EventDispatcherInterface;
use ApiGen\MemoryLimit;
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
		$eventDispatcherMock = Mockery::mock(EventDispatcherInterface::class);
		$this->application = new Application(new ApiGen, new MemoryLimit, $eventDispatcherMock);
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
