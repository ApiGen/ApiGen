<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Contract;

use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;
use Symfony\Component\Console\Command\Command;

interface ConfigurationDecoratorInterface
{
    public function addConfigurationOption(OptionInterface $option): void;

    public function decorateCommand(Command $command): void;
}
