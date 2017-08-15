<?php declare(strict_types=1);

namespace ApiGen\Element\Tree;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\ReflectionStorage;

final class TraitUsersResolver
{
    /**
     * @var ReflectionStorage
     */
    private $reflectionStorage;

    public function __construct(ReflectionStorage $reflectionStorage)
    {
        $this->reflectionStorage = $reflectionStorage;
    }

    /**
     * @return ClassReflectionInterface[]|TraitReflectionInterface[]
     */
    public function getUsers(TraitReflectionInterface $parentTraitReflection): array
    {
        $users = [];

        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            if (! array_key_exists($parentTraitReflection->getName(), $classReflection->getTraits())) {
                continue;
            }

            $users[$classReflection->getName()] = $classReflection;
        }

        foreach ($this->reflectionStorage->getTraitReflections() as $traitReflection) {
            if (! array_key_exists($parentTraitReflection->getName(), $traitReflection->getTraits())) {
                continue;
            }

            $users[$traitReflection->getName()] = $traitReflection;
        }

        uksort($users, 'strcasecmp');

        return $users;
    }
}
