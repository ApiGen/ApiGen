<?php declare(strict_types=1);

namespace ApiGen\Element\Tree;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;

final class TraitUsersResolver
{
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    public function __construct(ReflectionStorageInterface $reflectionStorage)
    {
        $this->reflectionStorage = $reflectionStorage;
    }

    public function getUsers(TraitReflectionInterface $traitReflection)
    {
        return $this->getDirectUsers($traitReflection) + $this->getIndirectUsers($traitReflection);
    }

    /**
     * @return ClassReflectionInterface[]
     */
    private function getDirectUsers(TraitReflectionInterface $traitReflection): array
    {
        $directUsers = [];
        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            if (! in_array($traitReflection, $classReflection->getOwnTraits())) {
                continue;
            }

            $directUsers[] = $classReflection;
        }

        uksort($directUsers, 'strcasecmp');
        return $directUsers;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    private function getIndirectUsers(TraitReflectionInterface $traitReflection): array
    {
        $indirectUsers = [];
        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            if (! $classReflection->usesTrait($traitReflection->getName())) {
                continue;
            }

            if (! in_array($traitReflection, $classReflection->getOwnTraits())) {
                continue;
            }

            $indirectUsers[] = $classReflection;
        }

        uksort($indirectUsers, 'strcasecmp');
        return $indirectUsers;
    }
}
