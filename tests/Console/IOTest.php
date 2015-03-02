<?php

namespace ApiGen\Tests\Console;

use ApiGen\Console\IO;
use Mockery;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class IOTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var IO
	 */
	private $io;


	protected function setUp()
	{
		$this->io = new IO;
	}


	public function testSetInputGetInput()
	{
		$inputMock = Mockery::mock(InputInterface::class);
		$this->io->setInput($inputMock);
		$this->assertInstanceOf(InputInterface::class, $this->io->getInput());
	}


	public function testSetOutputGetOutput()
	{
		$outputMock = Mockery::mock(OutputInterface::class);
		$this->io->setOutput($outputMock);
		$this->assertInstanceOf(OutputInterface::class, $this->io->getOutput());
	}

}
