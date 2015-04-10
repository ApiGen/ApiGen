<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Console;

use ApiGen\ApiGen;
use ApiGen\Contracts\Console\IO\IOInterface;
use ApiGen\MemoryLimit;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class Application extends BaseApplication
{

	/**
	 * @var IOInterface
	 */
	private $io;


	public function __construct(ApiGen $apiGen, MemoryLimit $memoryLimit, IOInterface $io)
	{
		parent::__construct('ApiGen', $apiGen->getVersion());
		$memoryLimit->setMemoryLimitTo('1024M');
		$this->io = $io;
	}


	/**
	 * {@inheritdoc}
	 */
	public function run(InputInterface $input = NULL, OutputInterface $output = NULL)
	{
		return parent::run($this->io->getInput(), $this->io->getOutput());
	}


	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultInputDefinition()
	{
		// todo: consider DI approach
		return new InputDefinition([
			new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
			new InputOption('help', 'h', InputOption::VALUE_NONE, 'Display this help message.'),
			new InputOption('quiet', 'q', InputOption::VALUE_NONE, 'Do not output any message.'),
			new InputOption('version', 'V', InputOption::VALUE_NONE, 'Display this application version.')
		]);
	}

}
