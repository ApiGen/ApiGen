<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Contract;

interface CommandOptionInterface extends OptionInterface
{
    public function getCommand(): string;
}
