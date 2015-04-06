<?php

namespace ApiGen\Tests\Command;

use ApiGen\Command\GenerateCommand;
use ApiGen\Console\IO;
use ApiGen\Tests\ContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use Symfony\Component\Console\Output\OutputInterface;
use TokenReflection\Exception\FileProcessingException;


class GenerateCommandTest extends ContainerAwareTestCase
{

	/**
	 * @var GenerateCommand
	 */
	private $generateCommand;


	protected function setUp()
	{
		$this->generateCommand = $this->container->getByType(GenerateCommand::class);
		$this->setupOutputToIo();
	}


	public function testReportParseErrors()
	{
		$errors = [
			new FileProcessingException([new \Exception('Some error')])
		];

		ob_start();
		MethodInvoker::callMethodOnObject($this->generateCommand, 'reportParserErrors', [$errors]);
		$output = ob_get_clean();

		$this->assertSame('<error>Parse error: Some error</error>', $output);
	}


	private function setupOutputToIo()
	{
		/** @var IO $io */
		$io = $this->container->getByType(IO::class);

		$outputMock = Mockery::mock(OutputInterface::class);
		$outputMock->shouldReceive('writeln')->andReturnUsing(function ($args) {
			echo $args;
		});
		$io->setOutput($outputMock);
	}

}
