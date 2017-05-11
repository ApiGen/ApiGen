<?php declare(strict_types=1);

namespace ApiGen\Element\Tree;

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
        $users = [];
        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            if (! in_array($traitReflection, $classReflection->getTraits())) {
                continue;
            }

            $users[] = $classReflection;
        }

        uksort($users, 'strcasecmp');
        return $users;
    }
}
