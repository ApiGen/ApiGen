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
    public function getUsedProperties(ClassReflectionInterface $classReflection): array
    {
        $ownPropertiesNames = array_keys($classReflection->getOwnProperties());
        $ownPropertiesNames = array_combine($ownPropertiesNames, $ownPropertiesNames);

        $properties = [];
        foreach ($classReflection->getTraits() as $traitReflection) {
            $usedProperties = [];
            foreach ($traitReflection->getOwnProperties() as $property) {
                if (! array_key_exists($property->getName(), $ownPropertiesNames)) {
                    $usedProperties[$property->getName()] = $property;
                    $ownPropertiesNames[$property->getName()] = null;
                }
            }

            if (! empty($usedProperties)) {
                ksort($usedProperties);
                $properties[$traitReflection->getName()] = array_values($usedProperties);
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
        $traitMethodAliases = $classReflection->getTraitAliases();
        foreach ($classReflection->getTraitMethods() as $methodReflection) {
            /* @todo: for traits - skip own metods
            if ($methodReflection->getDeclaringTraitName() === $classReflection->getName()) {
                continue;
            }
             */

            $traitName = $methodReflection->getDeclaringTraitName();
            $methodName = $methodReflection->getName();

            $usedMethods[$traitName][$methodName]['method'] = $methodReflection;
            if (isset($traitMethodAliases[$methodReflection->getName()])) {
                $usedMethods[$traitName][$methodName]['aliases'][$methodReflection->getName()] = $methodReflection;
            }
        }

        return $usedMethods;
    }
}
