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
    public function resolveDirectImplementersOfInterface(string $interfaceName): array
    {
        $implementers = [];
        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            if ($this->isAllowedDirectImplementer($classReflection, $interfaceName)) {
                $implementers[] = $classReflection;
            }
        }

        uksort($implementers, 'strcasecmp');

        return $implementers;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function resolveIndirectImplementersOfInterface(string $interfaceName): array
    {
        $implementers = [];
        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            if ($this->isAllowedIndirectImplementer($classReflection, $interfaceName)) {
                $implementers[] = $classReflection;
            }
        }

        uksort($implementers, 'strcasecmp');

        return $implementers;
    }

    private function isAllowedDirectImplementer(ClassReflectionInterface $classReflection, string $interfaceName): bool
    {
        return $classReflection->isDocumented() &&
            in_array($interfaceName, $classReflection->getOwnInterfaceNames(), true);
    }

    private function isAllowedIndirectImplementer(ClassReflectionInterface $classReflection, string $interfaceName): bool
    {
        return $classReflection->isDocumented()
            && $classReflection->implementsInterface($interfaceName)
            && ! in_array($interfaceName, $classReflection->getOwnInterfaceNames(), true);
    }
}
