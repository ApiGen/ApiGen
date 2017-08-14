<?php declare(strict_types=1);

namespace ApiGen\Element\Tree;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\ReflectionStorage;

final class SubClassesResolver
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
     * @return ClassReflectionInterface[]
     */
    public function getSubClasses(ClassReflectionInterface $parentClassReflection): array
    {
        $subClasses = [];
        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            if ($classReflection->isSubclassOf($parentClassReflection->getName())) {
                $subClasses[$classReflection->getName()] = $classReflection;
            }
        }

        uksort($subClasses, 'strcasecmp');

        return $subClasses;
    }
}
