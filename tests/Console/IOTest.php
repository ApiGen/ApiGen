<?php

namespace ApiGen\Tests\Console;

use ApiGen\Console\IO;
use Mockery;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;


class IOTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var IO
	 */
	private $io;


	protected function setUp()
	{
		$questionHelper = new QuestionHelper;
		$questionHelper->setInputStream($this->getInputStream("Test\n"));
		$this->io = new IO(new HelperSet(['question' => $questionHelper]));
	}


	public function testSetInputGetInput()
	{
		$inputMock = Mockery::mock('Symfony\Component\Console\Input\InputInterface');
		$this->io->setInput($inputMock);
		$this->assertInstanceOf(
			'Symfony\Component\Console\Input\InputInterface',
			$this->io->getInput()
		);
	}


	public function testSetOutputGetOutput()
	{
		$outputMock = Mockery::mock('Symfony\Component\Console\Output\OutputInterface');
		$this->io->setOutput($outputMock);
		$this->assertInstanceOf(
			'Symfony\Component\Console\Output\OutputInterface',
			$this->io->getOutput()
		);
	}


	public function testWriteln()
	{
		$outputMock = Mockery::mock('Symfony\Component\Console\Output\OutputInterface');
		$outputMock->shouldReceive('writeln')->andReturnUsing(function ($args) {
			return $args;
		});
		$this->io->setOutput($outputMock);

		$this->assertSame('Some message', $this->io->writeln('Some message'));
	}


	public function testAsking()
	{
		$this->assertFalse($this->io->ask('Is this true', TRUE));
		$this->setExpectedException('RuntimeException');
		$this->assertTrue($this->io->ask('Is this true', FALSE));
	}


	/**
	 * @param string $input
	 * @return resource
	 */
	private function getInputStream($input)
	{
		$stream = fopen('php://memory', 'r+', FALSE);
		fputs($stream, $input);
		rewind($stream);
		return $stream;
	}

}
