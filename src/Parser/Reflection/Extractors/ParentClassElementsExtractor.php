<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\ParentClassElementsExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Parser\Reflection\ReflectionClass;

final class ParentClassElementsExtractor implements ParentClassElementsExtractorInterface
{
    /**
     * @var ClassReflectionInterface
     */
    private $reflectionClass;

    public function __construct(ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;
    }

    /**
     * @return ConstantReflectionInterface[]
     */
    public function getInheritedConstants(): array
    {
        return array_filter(
            array_map(
                function (ReflectionClass $class) {
                    $reflections = $class->getOwnConstants();
                    ksort($reflections);
                    return $reflections;
                },
                $this->getParentClassesAndInterfaces()
            )
        );
    }

    /**
     * @return PropertyReflectionInterface[][]
     */
    public function getInheritedProperties(): array
    {
        $properties = [];
        $allProperties = array_flip(array_map(function (PropertyReflectionInterface $propertyReflection) {
            return $propertyReflection->getName();
        }, $this->reflectionClass->getOwnProperties()));

        foreach ($this->reflectionClass->getParentClasses() as $class) {
            $inheritedProperties = [];
            foreach ($class->getOwnProperties() as $property) {
                if (! array_key_exists($property->getName(), $allProperties) && ! $property->isPrivate()) {
                    $inheritedProperties[$property->getName()] = $property;
                    $allProperties[$property->getName()] = null;
                }
            }

            $properties = $this->sortElements($inheritedProperties, $properties, $class);
        }

        return $properties;
    }

    /**
     * @return MethodReflectionInterface[]
     */
    public function getInheritedMethods(): array
    {
        $methods = [];
        $allMethods = array_flip(array_map(function (MethodReflectionInterface $methodReflection) {
            return $methodReflection->getName();
        }, $this->reflectionClass->getOwnMethods()));

        foreach ($this->getParentClassesAndInterfaces() as $class) {
            $inheritedMethods = [];
            foreach ($class->getOwnMethods() as $method) {
                if (! array_key_exists($method->getName(), $allMethods) && ! $method->isPrivate()) {
                    $inheritedMethods[$method->getName()] = $method;
                    $allMethods[$method->getName()] = null;
                }
            }

            $methods = $this->sortElements($inheritedMethods, $methods, $class);
        }

        return $methods;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    private function getParentClassesAndInterfaces(): array
    {
        return array_merge($this->reflectionClass->getParentClasses(), $this->reflectionClass->getInterfaces());
    }

    /**
     * @param mixed[] $elements
     * @param mixed[] $allElements
     * @param ClassReflectionInterface $reflectionClass
     * @return mixed[]
     */
    private function sortElements(array $elements, array $allElements, ClassReflectionInterface $reflectionClass): array
    {
        if (! empty($elements)) {
            ksort($elements);
            $allElements[$reflectionClass->getName()] = array_values($elements);
        }

        return $allElements;
    }
}
