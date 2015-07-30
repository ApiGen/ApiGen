<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Console\Input;

use ApiGen\Contracts\Console\Input\DefaultInputDefinitionFactoryInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class DefaultInputDefinitionFactory implements DefaultInputDefinitionFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
            new InputOption('help', 'h', InputOption::VALUE_NONE, 'Display this help message.'),
            new InputOption('quiet', 'q', InputOption::VALUE_NONE, 'Do not output any message.'),
            new InputOption('version', 'V', InputOption::VALUE_NONE, 'Display this application version.')
        ]);
    }
}
