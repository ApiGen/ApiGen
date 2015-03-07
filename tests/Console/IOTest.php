<?php

namespace ApiGen\Tests\Console;

use ApiGen\Console\IO;
use Mockery;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Helper\HelperSet;


class IOTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var IO
	 */
	private $io;


	protected function setUp()
	{
		$this->io = new IO(new HelperSet);
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

}
