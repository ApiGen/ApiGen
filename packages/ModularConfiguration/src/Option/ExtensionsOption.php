<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Option;

use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;

final class ExtensionsOption implements OptionInterface
{
    /**
     * @var string
     */
    public const NAME = 'extensions';

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param mixed $value
     * @return string[]
     */
    public function resolveValue($value): array
    {
        if (! count($value)) {
            return ['php'];
        }

        return $value;
    }
}
