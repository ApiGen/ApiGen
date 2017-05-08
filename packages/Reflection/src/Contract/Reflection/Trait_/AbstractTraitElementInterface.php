<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Trait_;

interface AbstractTraitElementInterface
{
    public function getDeclaringTrait(): TraitReflectionInterface;

    public function getDeclaringTraitName(): string;
}
