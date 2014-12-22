<?php

namespace ApiGen\Tests\Console;

use ApiGen\Console\IO;
use ApiGen\Console\ProgressBar;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;
use Symfony;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;


class ProgressBarTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ProgressBar
	 */
	private $progressBar;


	protected function setUp()
	{
		$this->progressBar = new ProgressBar(new IO);
	}


	public function testInit()
	{
		$this->assertNull(PHPUnit_Framework_Assert::readAttribute($this->progressBar, 'bar'));

		$this->progressBar->init(50);

		/** @var Symfony\Component\Console\Helper\ProgressBar $bar */
		$bar = PHPUnit_Framework_Assert::readAttribute($this->progressBar, 'bar');
		$this->assertInstanceOf('Symfony\Component\Console\Helper\ProgressBar', $bar);
		$this->assertSame(50, $bar->getMaxSteps());
	}


	public function testIncrement()
	{
		$this->progressBar->increment();

		$this->progressBar->init(50);
		$this->progressBar->increment(20);

		/** @var Symfony\Component\Console\Helper\ProgressBar $bar */
		$bar =	PHPUnit_Framework_Assert::readAttribute($this->progressBar, 'bar');
		$this->assertSame(20, $bar->getProgress());

		$this->progressBar->increment(30);
		$this->assertSame(50, $bar->getProgress());
	}


	public function testGetBarFormat()
	{
		$this->assertSame(
			'<comment>%percent:3s% %</comment>',
			MethodInvoker::callMethodOnObject($this->progressBar, 'getBarFormat')
		);

		$io = new IO;
		$arrayInput = new ArgvInput([], new InputDefinition([new InputOption('debug')]));
		$arrayInput->setOption('debug', TRUE);
		$io->setInput($arrayInput);
		$progressBar = new ProgressBar($io);

		$this->assertSame('debug', MethodInvoker::callMethodOnObject($progressBar, 'getBarFormat'));
	}

}
