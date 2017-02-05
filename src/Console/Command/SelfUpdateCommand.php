<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Console\Command;

use Exception;
use Deployer\Component\PharUpdate\Manager;
use Deployer\Component\PharUpdate\Manifest;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SelfUpdateCommand extends BaseCommand
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
            if ($updateManager->update($version, false, true)) {
                $output->writeln('<info>Updated to latest version.</info>');
            } else {
                $output->writeln('<comment>Already up-to-date.</comment>');
            }

            return 0;
        } catch (Exception $e) {
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
