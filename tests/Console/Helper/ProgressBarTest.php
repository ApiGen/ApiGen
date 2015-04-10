<?php

namespace ApiGen\Tests\Console\Helper;

use ApiGen\Console\Helper\ProgressBar;
use ApiGen\Console\Input\LiberalFormatArgvInput;
use ApiGen\Console\IO\IO;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;
use Symfony;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProgressBar as SymfonyProgressBar;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;


class ProgressBarTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ProgressBar
	 */
	private $progressBar;


	protected function setUp()
	{
		$io = new IO(new HelperSet, new LiberalFormatArgvInput, new ConsoleOutput);
		$this->progressBar = new ProgressBar($io);
	}


	public function testInit()
	{
		$this->assertNull(PHPUnit_Framework_Assert::readAttribute($this->progressBar, 'bar'));

		$this->progressBar->init(50);

		/** @var SymfonyProgressBar $bar */
		$bar = PHPUnit_Framework_Assert::readAttribute($this->progressBar, 'bar');
		$this->assertInstanceOf(SymfonyProgressBar::class, $bar);
		$this->assertSame(50, $bar->getMaxSteps());
	}


	public function testIncrement()
	{
		$this->progressBar->increment();

		$this->progressBar->init(50);
		$this->progressBar->increment(20);

		/** @var SymfonyProgressBar $bar */
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

		$arrayInput = new ArgvInput([], new InputDefinition([new InputOption('debug')]));
		$arrayInput->setOption('debug', TRUE);
		$io = new IO(new HelperSet, $arrayInput, new ConsoleOutput);
		$progressBar = new ProgressBar($io);

		$this->assertSame('debug', MethodInvoker::callMethodOnObject($progressBar, 'getBarFormat'));
	}

}
