<?php declare(strict_types=1);

namespace ApiGen\Element\Tree;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;

final class ClassTraitElementResolver
{
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
    public function getUsedMethods(ClassReflectionInterface $classReflection): array
    {
        $usedMethods = [];
        $traitMethodAlisess = $classReflection->getTraitAliases();
        foreach ($classReflection->getTraitMethods() as $methodReflection) {
            /* @todo: for traits - skip own metods
            if ($methodReflection->getDeclaringTraitName() === $classReflection->getName()) {
                continue;
            }
             */



            $traitName = $methodReflection->getDeclaringTraitName();
            $methodName = $methodReflection->getName();

            $usedMethods[$traitName][$methodName]['method'] = $methodReflection;
            if (isset($traitMethodAlisess[$methodReflection->getName()])) {
                $usedMethods[$traitName][$methodName]['aliases'][$methodReflection->getName()] = $methodReflection;
            }
        }

        return $usedMethods;
    }
}
