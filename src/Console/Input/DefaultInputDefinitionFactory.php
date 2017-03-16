<?php declare(strict_types=1);

namespace ApiGen\Console\Input;

use ApiGen\Contracts\Console\Input\DefaultInputDefinitionFactoryInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

final class DefaultInputDefinitionFactory implements DefaultInputDefinitionFactoryInterface
{
    public function create(): InputDefinition
    {
        return new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
            new InputOption('help', 'h', InputOption::VALUE_NONE, 'Display this help message.'),
            new InputOption('quiet', 'q', InputOption::VALUE_NONE, 'Do not output any message.')
        ]);
    }
}
