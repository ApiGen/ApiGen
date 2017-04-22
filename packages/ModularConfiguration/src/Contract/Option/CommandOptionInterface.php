<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Contract\Option;

interface CommandOptionInterface extends OptionInterface
{
    public function getCommand(): string;

    public function getDescription(): string;

    public function isValueRequired(): bool;

    /**
     * @return mixed|null
     */
    public function getDefaultValue();
}
