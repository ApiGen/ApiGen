<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Option;

use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;

final class AnnotationGroupsOption implements OptionInterface
{
    /**
     * @var string
     */
    public const NAME = 'annotation_groups';

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
        if (! is_array($value)) {
            return [];
        }

        return $value;
    }
}
