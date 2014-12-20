<?php

namespace ApiGen\Tests\Command;

use ApiGen\Command\GenerateCommand;
use ApiGen\Tests\ContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use TokenReflection\Exception\FileProcessingException;


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


	public function testReportParseErrors()
	{
		$errors = [
			new FileProcessingException([new \Exception('Some error')])
		];

		$outputMock = Mockery::mock('Symfony\Component\Console\Output\OutputInterface');
		$outputMock->shouldReceive('writeln')->andReturnUsing(function ($args) {
			echo $args;
		});

		ob_start();
		MethodInvoker::callMethodOnObject($this->generateCommand, 'reportParserErrors', [$errors, $outputMock]);
		$output = ob_get_clean();

		$this->assertSame('<error>Parse error: Some error</error>', $output);
	}

}
