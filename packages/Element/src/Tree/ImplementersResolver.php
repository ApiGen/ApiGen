<?php declare(strict_types=1);

namespace ApiGen\Element\Tree;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\ReflectionStorage;

final class ImplementersResolver
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
     * @return ClassReflectionInterface[]|InterfaceReflectionInterface[]
     */
    public function getImplementers(InterfaceReflectionInterface $parentInterfaceReflection): array
    {
        $implementers = [];
        $implementers = $this->getClassImplementers($implementers, $parentInterfaceReflection);
        $implementers = $this->getInterfaceImplementers($implementers, $parentInterfaceReflection);

        uksort($implementers, 'strcasecmp');

        return $implementers;
    }

    /**
     * @param mixed[] $implementers
     * @return ClassReflectionInterface[]
     */
    private function getClassImplementers(
        array $implementers,
        InterfaceReflectionInterface $parentInterfaceReflection
    ): array {
        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            if ($classReflection->implementsInterface($parentInterfaceReflection->getName())) {
                $implementers[$classReflection->getName()] = $classReflection;
            }
        }

        return $implementers;
    }

    /**
     * @param ClassReflectionInterface[]|InterfaceReflectionInterface[] $implementers
     * @return ClassReflectionInterface[]|InterfaceReflectionInterface[]
     */
    private function getInterfaceImplementers(
        array $implementers,
        InterfaceReflectionInterface $parentInterfaceReflection
    ): array {
        foreach ($this->reflectionStorage->getInterfaceReflections() as $interfaceReflection) {
            if ($interfaceReflection->implementsInterface($parentInterfaceReflection->getName())) {
                $implementers[$interfaceReflection->getName()] = $interfaceReflection;
            }
        }

        return $implementers;
    }
}
