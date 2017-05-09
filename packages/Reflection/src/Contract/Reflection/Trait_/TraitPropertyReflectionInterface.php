<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Trait_;

use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;

interface TraitPropertyReflectionInterface extends AbstractTraitElementInterface, StartAndEndLineInterface, AnnotationsInterface,
    AccessLevelInterface
{
    public function isDefault(): bool;

    public function isStatic(): bool;

    /**
     * @return mixed
     */
    public function getDefaultValue();

    public function getTypeHint(): string;

    public function getNamespaceName(): string;

    public function getName(): string;

    /**
     * Returns the unqualified name (UQN).
     */
    public function getShortName(): string;

    public function isDeprecated(): bool;
}
