<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\InTraitInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;

interface PropertyReflectionInterface extends
    ElementReflectionInterface,
    InTraitInterface,
    InClassInterface,
    LinedInterface
{
    public function getPrettyName(): string;

    public function isDefault(): bool;

    public function isStatic(): bool;

    /**
     * @return mixed
     */
    public function getDefaultValue();

    public function getTypeHint(): string;

    public function isReadOnly(): bool;

    public function isWriteOnly(): bool;

    public function hasAnnotation(string $name): bool;

    /**
     * @param string $name
     * @return mixed[]
     */
    public function getAnnotation(string $name): array;
}
