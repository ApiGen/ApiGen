<?php declare(strict_types=1);

namespace ApiGen\Element\Tree;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;

final class ImplementersResolver
{
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    public function __construct(ReflectionStorageInterface $reflectionStorage)
    {
        $this->reflectionStorage = $reflectionStorage;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function resolveImplementersOfInterface(string $interfaceName): array
    {
        $implementers = [];
        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            if ($classReflection->implementsInterface($interfaceName)) {
                $implementers[] = $classReflection;
            }
        }

        uksort($implementers, 'strcasecmp');

        return $implementers;
    }
//
//    private function isAllowedImplementer(ClassReflectionInterface $classReflection, string $interfaceName): bool
//    {
//        return in_array($interfaceName, $classReflection->getOwnInterfaceNames(), true)
//            || $classReflection->implementsInterface($interfaceName);
//    }
}
