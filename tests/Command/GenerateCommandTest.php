<?php

namespace ApiGen\Tests\Command;

use ApiGen\Command\GenerateCommand;
use ApiGen\Tests\ContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;
use Mockery;


class GenerateCommandTest extends ContainerAwareTestCase
{

	/**
	 * @var GenerateCommand
	 */
	private $generateCommand;


	protected function setUp()
	{
		$this->generateCommand = $this->container->getByType('ApiGen\Command\GenerateCommand');
	}


	public function testExecute()
	{
		$this->assertFileNotExists(TEMP_DIR . '/Api/index.html');

		$inputMock = Mockery::mock('Symfony\Component\Console\Input\InputInterface');
		$inputMock->shouldReceive('getOptions')->andReturn([
			'config' => NULL,
			'destination' => TEMP_DIR . '/Api',
			'source' => __DIR__ . '/Source'
		]);
		$outputMock = Mockery::mock('Symfony\Component\Console\Output\OutputInterface');
		$outputMock->shouldReceive('writeln')->andReturnNull();
		$result = MethodInvoker::callMethodOnObject($this->generateCommand, 'execute', [$inputMock, $outputMock]);
		$this->assertSame(0, $result);

		$this->assertFileExists(TEMP_DIR . '/Api/index.html');
	}

}
