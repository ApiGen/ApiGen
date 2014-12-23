<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Command;

use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class SelfUpdateCommand extends Command
{

	/**
	 * @var string
	 */
	const MANIFEST_URL = 'http://apigen.org/manifest.json';


	protected function configure()
	{
		$this->setName('self-update')
			->setAliases(['selfupdate'])
			->setDescription('Updates apigen.phar to the latest available version');
	}


	/**
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		try {
			$updateManager = $this->createUpdateManager();
			$version = $this->getApplication()->getVersion();
			$updateManager->update($version, TRUE, TRUE);

			$output->writeln('<info>ApiGen updated!</info>');
			return 0;

		} catch (\Exception $e) {
			$output->writeln('<error>' . $e->getMessage() . '</error>');
			return 1;
		}
	}


	/**
	 * @return Manager
	 */
	private function createUpdateManager()
	{
		return new Manager(Manifest::loadFile(self::MANIFEST_URL));
	}

}
