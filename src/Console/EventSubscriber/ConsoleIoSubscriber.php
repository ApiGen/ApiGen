<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Console\EventSubscriber;

use ApiGen\Console\IOInterface;
use ApiGen\Contracts\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;


class ConsoleIoSubscriber implements EventSubscriberInterface
{

	/**
	 * @var IOInterface
	 */
	private $consoleIO;


	public function __construct(IOInterface $consoleIO)
	{
		$this->consoleIO = $consoleIO;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getSubscribedEvents()
	{
		return [ConsoleEvents::COMMAND => 'setupConsoleIo'];
	}


	public function setupConsoleIo(ConsoleCommandEvent $event)
	{
		$this->consoleIO->setInput($event->getInput());
		$this->consoleIO->setOutput($event->getOutput());
	}

}
