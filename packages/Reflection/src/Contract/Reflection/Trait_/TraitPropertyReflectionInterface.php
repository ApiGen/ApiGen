<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Trait_;

use ApiGen\Reflection\Contract\Reflection\Partial\AccessLevelInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;

interface TraitPropertyReflectionInterface extends AbstractTraitElementInterface, StartAndEndLineInterface, AnnotationsInterface, AccessLevelInterface
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
}
