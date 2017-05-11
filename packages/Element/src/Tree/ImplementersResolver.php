<?php declare(strict_types=1);

namespace ApiGen\Element\Tree;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
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
    public function getImplementers(InterfaceReflectionInterface $interfaceReflection): array
    {
        $implementers = [];
        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            if ($classReflection->implementsInterface($interfaceReflection->getName())) {
                $implementers[$classReflection->getName()] = $classReflection;
            }
        }

        uksort($implementers, 'strcasecmp');

        return $implementers;
    }
}
