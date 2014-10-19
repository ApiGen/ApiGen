<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Console;

use ApiGen\Console;
use Nette;
use Symfony\Component\Console\Helper\ProgressBar as ProgressBarHelper;


class ProgressBar extends Nette\Object
{

	/**
	 * @var Console\IO
	 */
	private $consoleIO;


	/**
	 * @var ProgressBarHelper
	 */
	private $bar;


	public function __construct(Console\IO $consoleIO)
	{
		$this->consoleIO = $consoleIO;
	}


	/**
	 * @param int $maximum
	 */
	public function init($maximum = 1)
	{
		$this->bar = new ProgressBarHelper($this->consoleIO->getOutput(), $maximum);
		$this->bar->setFormat('%percent:4s% %, %memory:2d% MB RAM');
		$this->bar->setRedrawFrequency(10);
		$this->bar->start();
	}


	/**
	 * @param int $increment
	 */
	public function increment($increment = 1)
	{
		$this->bar->advance($increment);
	}

}
