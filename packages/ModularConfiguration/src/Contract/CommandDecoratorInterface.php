<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Contract;

use ApiGen\ModularConfiguration\Contract\Option\CommandBoundInterface;
use Symfony\Component\Console\Command\Command;

interface CommandDecoratorInterface
{
    public function addOption(CommandBoundInterface $option): void;

    public function decorateCommand(Command $command): void;
}
