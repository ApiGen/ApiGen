<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Contract\Option;

interface CommandBoundInterface extends OptionInterface
{
    public function getCommand(): string;
}
