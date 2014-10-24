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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class Application extends Kdyby\Console\Application
{

	/**
	 * @var array
	 */
	public $onRun = array();

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

		// Switch working dir
		if ($newWorkDir = $this->getNewWorkingDir($input)) {
			$oldWorkingDir = getcwd();
			chdir($newWorkDir);
		}

		$result = parent::doRun($input, $output);

		if (isset($oldWorkingDir)) {
			chdir($oldWorkingDir);
		}

		return $result;
	}


	public function setEventManager(EventManager $eventManager)
	{
		$this->eventManager = $eventManager;
	}


	public function onRun(InputInterface $input, OutputInterface $output)
	{
		$this->eventManager->dispatchEvent(__METHOD__, new EventArgsList(array($input, $output)));
	}


	/**
	 * {@inheritDoc}
	 */
	protected function getDefaultInputDefinition()
	{
		$definition = parent::getDefaultInputDefinition();
		$definition->addOption(new InputOption('--working-dir', '-wd', InputOption::VALUE_REQUIRED, 'If specified, use the given directory as working directory.'));
		return $definition;
	}


	/**
	 * @return string
	 * @throws \RuntimeException
	 */
	private function getNewWorkingDir(InputInterface $input)
	{
		$workingDir = $input->getParameterOption(array('--working-dir', '-d'));
		if ($workingDir !== FALSE && ! is_dir($workingDir)) {
			throw new \RuntimeException('Invalid working directory specified.');
		}
		return $workingDir;
	}

}
