<?php declare(strict_types=1);

namespace ApiGen\Element\Tree;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;

final class ClassTraitElementResolver
{

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
