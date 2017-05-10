<?php declare(strict_types=1);

namespace ApiGen\Element\Tree;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;

final class ClassTraitElementResolver
{
    /**
     * @return TraitPropertyReflectionInterface[]
     */
    public function getTraitProperties(): array
    {
        $properties = [];
        $traitProperties = $this->originalReflection->getTraitProperties();
        foreach ($traitProperties as $property) {
            $apiProperty = $this->transformerCollector->transformSingle($property);
            $properties[$property->getName()] = $apiProperty;
        }

        return $properties;
    }

    /**
     * @return TraitMethodReflectionInterface[]
     */
    public function getTraitMethods(): array
    {
        $methods = [];
        foreach ($this->originalReflection->getTraitMethods() as $method) {
            $apiMethod = $this->transformerCollector->transformSingle($method);
            $methods[$method->getName()] = $apiMethod;
        }

        return $methods;
    }

    /**
     * @return TraitPropertyReflectionInterface[][]
     */
    public function getUsedProperties(): array
    {
        $allProperties = array_flip(array_map(function (ClassPropertyReflectionInterface $property) {
            return $property->getName();
        }, $this->classReflection->getOwnProperties()));

        $properties = [];
        foreach ($this->classReflection->getTraits() as $trait) {
            if (! $trait instanceof ClassReflectionInterface) {
                continue;
            }

            $usedProperties = [];
            foreach ($trait->getOwnProperties() as $property) {
                if (! array_key_exists($property->getName(), $allProperties)) {
                    $usedProperties[$property->getName()] = $property;
                    $allProperties[$property->getName()] = null;
                }
            }

            if (! empty($usedProperties)) {
                ksort($usedProperties);
                $properties[$trait->getName()] = array_values($usedProperties);
            }
        }

        return $properties;
    }

    /**
     * @return TraitMethodReflectionInterface[]
     */
    public function getUsedMethods(): array
    {
        $usedMethods = [];
        foreach ($this->classReflection->getMethods() as $methodReflection) {
            if ($methodReflection->getDeclaringTraitName() === ''
                || $methodReflection->getDeclaringTraitName() === $this->classReflection->getName()
            ) {
                continue;
            }

            $traitName = $methodReflection->getDeclaringTraitName();
            $methodName = $methodReflection->getName();

            $usedMethods[$traitName][$methodName]['method'] = $methodReflection;
            if ($this->wasMethodNameAliased($methodReflection)) {
                $usedMethods[$traitName][$methodName]['aliases'][$methodReflection->getName()] = $methodReflection;
            }
        }

        return $usedMethods;
    }

    private function wasMethodNameAliased(ClassMethodReflectionInterface $methodReflection): bool
    {
        return $methodReflection->getOriginalName() !== null
            && $methodReflection->getOriginalName() !== $methodReflection->getName();
    }
}
