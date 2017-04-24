<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Option;

use ApiGen\Configuration\Exceptions\ConfigurationException;
use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;
use ApiGen\Utils\FileSystem;

final class AnnotationGroupsOption implements OptionInterface
{
    /**
     * @var string
     */
    public const NAME = 'annotationGroups';

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
