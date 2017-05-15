<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Trait_;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;

interface AbstractTraitElementInterface extends AbstractReflectionInterface
{
    public function getDeclaringTrait(): TraitReflectionInterface;

    public function getDeclaringTraitName(): string;
}
