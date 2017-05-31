<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Contract\Option;

interface CommandArgumentInterface extends CommandBoundInterface
{
    public function getDescription(): string;

    public function isValueRequired(): bool;

    public function isArray(): bool;
}
