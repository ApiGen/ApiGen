<?php

namespace ApiGen\Tests\Console\IO;

use ApiGen\Console\Input\LiberalFormatArgvInput;
use ApiGen\Console\IO\IO;
use Mockery;
use PHPUnit_Framework_TestCase;
use RuntimeException;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;


class IOTest extends PHPUnit_Framework_TestCase
{

	public function testGetters()
	{
		$io = new IO(new HelperSet([]), new LiberalFormatArgvInput, new NullOutput);
		$this->assertInstanceOf(InputInterface::class, $io->getInput());
		$this->assertInstanceOf(OutputInterface::class, $io->getOutput());
	}


	public function testWriteln()
	{
		$outputMock = Mockery::mock(OutputInterface::class);
		$outputMock->shouldReceive('writeln')->andReturnUsing(function ($args) {
			return $args;
		});

		$io = new IO(new HelperSet([]), new LiberalFormatArgvInput, $outputMock);
		$this->assertSame('Some message', $io->writeln('Some message'));
	}


	public function testAsking()
	{
		$questionHelper = new QuestionHelper;
		$questionHelper->setInputStream($this->getInputStream("Test\n"));
		$io = new IO(new HelperSet(['question' => $questionHelper]), new LiberalFormatArgvInput, new NullOutput);

		$this->assertFalse($io->ask('Is this true', TRUE));
		$this->setExpectedException(RuntimeException::class);
		$this->assertTrue($io->ask('Is this true', FALSE));
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
