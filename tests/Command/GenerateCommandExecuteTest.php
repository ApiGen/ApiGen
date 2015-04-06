<?php

namespace ApiGen\Tests\Command;

use ApiGen\Command\GenerateCommand;
use ApiGen\Tests\ContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class GenerateCommandExecuteTest extends ContainerAwareTestCase
{

	/**
	 * @var GenerateCommand
	 */
	private $generateCommand;


	protected function setUp()
	{
		$this->generateCommand = $this->container->getByType(GenerateCommand::class);
	}


	public function testExecute()
	{
		$this->assertFileNotExists(TEMP_DIR . '/Api/index.html');

		$inputMock = Mockery::mock(InputInterface::class);
		$inputMock->shouldReceive('getOptions')->andReturn([
			'config' => NULL,
			'destination' => TEMP_DIR . '/Api',
			'source' => __DIR__ . '/Source'
		]);

		$this->assertSame(
			0, // success
			MethodInvoker::callMethodOnObject($this->generateCommand, 'execute', [$inputMock, $this->getOutputMock()])
		);

		$this->assertFileExists(TEMP_DIR . '/Api/index.html');
	}


	public function testExecuteWithError()
	{
		$inputMock = Mockery::mock(InputInterface::class);

		$this->assertSame(
			1, // failure
			MethodInvoker::callMethodOnObject($this->generateCommand, 'execute', [$inputMock, $this->getOutputMock()])
		);
	}


	/**
	 * @return Mockery\MockInterface|OutputInterface
	 */
	private function getOutputMock()
	{
		$outputMock = Mockery::mock(OutputInterface::class);
		$outputMock->shouldReceive('writeln')->andReturnNull();
		return $outputMock;
	}

}
