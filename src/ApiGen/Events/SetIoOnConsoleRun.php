<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\Console;
use Kdyby\Events\Subscriber;
use Nette;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class SetIoOnConsoleRun extends Nette\Object implements Subscriber
{

	/**
	 * @var Console\IO
	 */
	private $consoleIO;


	public function __construct(Console\IO $consoleIO)
	{
		$this->consoleIO = $consoleIO;
	}


	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return array(
			'ApiGen\Console\Application::onRun'
		);
	}


	public function onRun(InputInterface $input, OutputInterface $output)
	{
		$this->consoleIO->setInput($input);
		$this->consoleIO->setOutput($output);
	}

}
