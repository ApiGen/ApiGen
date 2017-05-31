<?php declare(strict_types=1);

namespace ApiGen\Element\Tree;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;

final class ParentClassElementsResolver
{
    /**
     * @return ClassPropertyReflectionInterface[][]
     */
    public function getInheritedProperties(ClassReflectionInterface $classReflection): array
    {
        $properties = [];
        $allProperties = array_flip(array_map(function (ClassPropertyReflectionInterface $propertyReflection) {
            return $propertyReflection->getName();
        }, $classReflection->getOwnProperties()));

        foreach ($classReflection->getParentClasses() as $parentClassName => $parentClass) {
            $inheritedProperties = [];
            foreach ($parentClass->getOwnProperties() as $property) {
                if (! array_key_exists($property->getName(), $allProperties) && ! $property->isPrivate()) {
                    $inheritedProperties[$property->getName()] = $property;
                    $allProperties[$property->getName()] = null;
                }
            }

            $properties = $this->sortElements($inheritedProperties, $properties, $parentClassName);
        }

        return $properties;
    }

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getInheritedMethods(ClassReflectionInterface $classReflection): array
    {
        $methods = [];
        $allMethods = array_flip(array_map(function (ClassMethodReflectionInterface $classMethodReflection) {
            return $classMethodReflection->getName();
        }, $classReflection->getOwnMethods()));

        foreach ($this->getParentClassesAndInterfaces($classReflection) as $parentClassName => $class) {
            $inheritedMethods = [];
            foreach ($class->getOwnMethods() as $method) {
                if (! array_key_exists($method->getName(), $allMethods) && ! $method->isPrivate()) {
                    $inheritedMethods[$method->getName()] = $method;
                    $allMethods[$method->getName()] = null;
                }
            }

            $methods = $this->sortElements($inheritedMethods, $methods, $parentClassName);
        }

        return $methods;
    }

    /**
     * @return ClassReflectionInterface[]|InterfaceReflectionInterface[]
     */
    private function getParentClassesAndInterfaces(ClassReflectionInterface $classReflection): array
    {
        return array_merge(
            $classReflection->getParentClasses(),
            $classReflection->getInterfaces()
        );
    }

    /**
     * @param mixed[] $elements
     * @param mixed[] $allElements
     * @return mixed[]
     */
    private function sortElements(array $elements, array $allElements, string $classOrInterfaceName): array
    {
        if (! empty($elements)) {
            ksort($elements);
            $allElements[$classOrInterfaceName] = array_values($elements);
        }

        return $allElements;
    }
}
