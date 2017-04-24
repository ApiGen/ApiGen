<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Option;

use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;

final class TitleOption implements OptionInterface
{
    /**
     * @var string
     */
    public const NAME = 'title';

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param mixed $value
     */
    public function resolveValue($value): string
    {
        if ($value === null) {
            return '';
        }

        return $value;
    }
}
