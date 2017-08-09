<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;

interface ClassPropertyReflectionInterface extends AbstractClassElementInterface, StartAndEndLineInterface, AnnotationsInterface
{
    public function isDefault(): bool;

    public function isStatic(): bool;

    /**
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * @return string[]
     */
    public function getTypeHints(): array;

    public function getNamespaceName(): string;

    public function getName(): string;

    public function isDeprecated(): bool;
}
