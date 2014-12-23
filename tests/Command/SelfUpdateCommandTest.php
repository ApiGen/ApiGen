<?php

namespace ApiGen\Tests\Command;

use ApiGen\Command\SelfUpdateCommand;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use PHPUnit_Framework_TestCase;


class SelfUpdateCommandTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var SelfUpdateCommand
	 */
	private $selfUpdateCommand;


	protected function setUp()
	{
		$this->selfUpdateCommand = new SelfUpdateCommand;

		$helperSetMock = Mockery::mock('Symfony\Component\Console\Helper\HelperSet');
		$applicationMock = Mockery::mock('Symfony\Component\Console\Application');
		$applicationMock->shouldReceive('getVersion')->andReturn('4.0.0-RC5');
		$applicationMock->shouldReceive('getHelperSet')->andReturn($helperSetMock);
		$this->selfUpdateCommand->setApplication($applicationMock);
	}


	public function testExecute()
	{
		$inputMock = Mockery::mock('Symfony\Component\Console\Input\InputInterface');
		$outputMock = Mockery::mock('Symfony\Component\Console\Output\OutputInterface');
		$outputMock->shouldReceive('writeln')->andReturnNull();

		$result = MethodInvoker::callMethodOnObject($this->selfUpdateCommand, 'execute', [$inputMock, $outputMock]);
		$this->assertSame(0, $result);
	}

}
