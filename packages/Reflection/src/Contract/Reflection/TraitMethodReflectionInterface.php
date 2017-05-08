<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

interface TraitMethodReflectionInterface extends AbstractMethodReflectionInterface
{
    // smoetimes?
    public function getDeclaringTrait(): ?TraitReflectionInterface;

    public function getDeclaringTraitName(): string;
}
