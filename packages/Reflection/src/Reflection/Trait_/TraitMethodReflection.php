<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Trait_;

/**
 * @todo1111
 */
final class TraitMethodReflection
{
    public function getDeclaringTrait(): ?TraitReflectionInterface
    {
        if ($this->reflection->getDeclaringClass()->isTrait()) {
            return $this->transformerCollector->transformReflectionToElement($this->reflection->getDeclaringClass());
        }

        return null;
    }

    public function getDeclaringTraitName(): string
    {
        if (! $this->getDeclaringTrait()) {
            return '';
        }

        return $this->getDeclaringTrait()
            ->getName();
    }

}
