<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Console;

use Nette;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Helper\ProgressBar as ProgressBarHelper;


class ProgressBar extends Nette\Object
{

	/**
	 * @var ProgressBarHelper
	 */
	private $bar;


	/**
	 * @param int $maximum
	 */
	public function init($maximum = 1)
	{
		$output = new ConsoleOutput;
		$this->bar = new ProgressBarHelper($output, $maximum);
		$this->bar->setFormat('%percent% % [%bar%] %elapsed:4s%, %memory:d% MB RAM');
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
