<?php declare(strict_types=1);

namespace ApiGen\Element\Tree;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;

final class SubClassesResolver
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
    public function getDirectSubClasses(ClassReflectionInterface $parentClassReflection): array
    {
        $subClasses = [];
        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            if ($classReflection->getParentClassName() === $parentClassReflection->getName()) {
                $subClasses[$classReflection->getName()] = $classReflection;
            }
        }

        uksort($subClasses, 'strcasecmp');
        return $subClasses;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectSubClasses(ClassReflectionInterface $parentClasReflection): array
    {
        $subClasses = [];
        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            if ($classReflection->getParentClassName() !== $parentClasReflection->getName()
                && $classReflection->isSubclassOf($parentClasReflection->getName())
            ) {
                $subClasses[$classReflection->getName()] = $classReflection;
            }
        }

        uksort($subClasses, 'strcasecmp');
        return $subClasses;
    }
}
