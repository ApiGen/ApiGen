<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Console;

use ApiGen\ApiGen;
use Kdyby;
use Kdyby\Events\EventArgsList;
use Kdyby\Events\EventManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class Application extends Kdyby\Console\Application
{

	/**
	 * @var array
	 */
	public $onRun = [];

	/**
	 * @var EventManager
	 */
	private $eventManager;


	/**
	 * {@inheritDoc}
	 */
	public function __construct()
	{
		parent::__construct('ApiGen', ApiGen::VERSION);
	}


	/**
	 * {@inheritDoc}
	 */
	public function getLongVersion()
	{
		return parent::getLongVersion() . ' ' . ApiGen::RELEASE_DATE;
	}


	/**
	 * {@inheritDoc}
	 */
	public function doRun(InputInterface $input, OutputInterface $output)
	{
		$this->onRun($input, $output);
		return parent::doRun($input, $output);
	}


	public function setEventManager(EventManager $eventManager)
	{
		$this->eventManager = $eventManager;
	}


	/**
	 * {@inheritDoc}
	 */
	protected function getDefaultInputDefinition()
	{
		return new InputDefinition([
			new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
			new InputOption('help', 'h', InputOption::VALUE_NONE, 'Display this help message.'),
			new InputOption('quiet', 'q', InputOption::VALUE_NONE, 'Do not output any message.'),
			new InputOption('version', NULL, InputOption::VALUE_NONE, 'Display this application version.')
		]);
	}


	private function onRun(InputInterface $input, OutputInterface $output)
	{
		$this->eventManager->dispatchEvent(__METHOD__, new EventArgsList([$input, $output]));
	}

}
