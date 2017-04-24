<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Option;

use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;

final class OverwriteOption implements OptionInterface
{
    /**
     * @var string
     */
    public const NAME = 'overwrite';

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param mixed $value
     */
    public function resolveValue($value): bool
    {
        if ($value === null) {
            return false;
        }

        return (bool) $value;
    }
}
