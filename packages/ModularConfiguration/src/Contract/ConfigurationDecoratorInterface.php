<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Contract;

use Symfony\Component\Console\Command\Command;

interface ConfigurationDecoratorInterface
{
    public function addConfigurationOption(OptionInterface $configurationOption): void;

    public function decorateCommand(Command $command): void;
}
