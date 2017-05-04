<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

use ApiGen\Contracts\Parser\Reflection\TraitReflectionInterface;

interface InTraitInterface extends InNamespaceInterface
{
    public function getDeclaringTrait(): ?TraitReflectionInterface;

    public function getDeclaringTraitName(): string;
}
