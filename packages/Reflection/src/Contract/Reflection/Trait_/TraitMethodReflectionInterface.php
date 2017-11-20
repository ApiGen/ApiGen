<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Trait_;

use ApiGen\Reflection\Contract\Reflection\AbstractMethodReflectionInterface;

interface TraitMethodReflectionInterface extends AbstractMethodReflectionInterface, AbstractTraitElementInterface
{
    public function getOverriddenMethod(): ?self;
}
