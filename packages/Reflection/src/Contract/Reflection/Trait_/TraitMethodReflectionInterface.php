<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Trait_;

use ApiGen\Reflection\Contract\Reflection\AbstractMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\TraitReflectionInterface;

interface TraitMethodReflectionInterface extends AbstractMethodReflectionInterface
{
    public function getDeclaringTrait(): TraitReflectionInterface;

    public function getDeclaringTraitName(): string;
}
