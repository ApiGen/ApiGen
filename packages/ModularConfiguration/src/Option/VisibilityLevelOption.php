<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Option;

use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;
use ReflectionProperty;

final class VisibilityLevelOption implements OptionInterface
{
    /**
     * @var string
     */
    public const NAME = 'visibility_levels';

    /**
     * @var string
     */
    public const PUBLIC = 'public';

    /**
     * @var string
     */
    public const PROTECTED = 'protected';

    /**
     * @var string
     */
    public const PRIVATE = 'private';

    /**
     * @var int[]
     */
    private $nameToModificatorMap = [
        self::PUBLIC => ReflectionProperty::IS_PUBLIC,
        self::PROTECTED => ReflectionProperty::IS_PROTECTED,
        self::PRIVATE => ReflectionProperty::IS_PRIVATE,
    ];

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param mixed $value
     */
    public function resolveValue($value): int
    {
        if (empty($value)) {
            return ReflectionProperty::IS_PUBLIC + ReflectionProperty::IS_PROTECTED;
        }

        $integerLevel = 0;

        foreach ($value as $name) {
            if (isset($this->nameToModificatorMap[$name])) {
                $integerLevel += $this->nameToModificatorMap[$name];
            }
        }

        return $integerLevel;
    }
}
