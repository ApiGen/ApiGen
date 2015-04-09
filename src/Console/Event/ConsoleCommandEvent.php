<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Console\Event;

use ApiGen\Contracts\EventDispatcher\Event\EventInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent as BaseConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ConsoleCommandEvent extends BaseConsoleCommandEvent implements EventInterface
{

	/**
	 * @var string
	 */
	private $name;


	public function __construct($name, Command $command, InputInterface $input, OutputInterface $output)
	{
		$this->name = $name;
		parent::__construct($command, $input, $output);
	}

}
