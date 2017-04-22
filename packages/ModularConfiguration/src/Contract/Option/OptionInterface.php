<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Contract\Option;

interface OptionInterface
{
    public function getName(): string;

    /**
     * @param mixed $value
     * @return mixed
     */
    public function resolveValue($value);
}
