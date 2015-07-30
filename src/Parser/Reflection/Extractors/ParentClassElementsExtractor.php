<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\ParentClassElementsExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionMethod;
use ApiGen\Parser\Reflection\ReflectionProperty;

class ParentClassElementsExtractor implements ParentClassElementsExtractorInterface
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
     * {@inheritdoc}
     */
    public function getInheritedConstants()
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
     * {@inheritdoc}
     */
    public function getInheritedProperties()
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
     * {@inheritdoc}
     */
    public function getInheritedMethods()
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
    private function getParentClassesAndInterfaces()
    {
        return array_merge($this->reflectionClass->getParentClasses(), $this->reflectionClass->getInterfaces());
    }


    /**
     * @param array $elements
     * @param array $allElements
     * @param ClassReflectionInterface $reflectionClass
     * @return array
     */
    private function sortElements(array $elements, array $allElements, ClassReflectionInterface $reflectionClass)
    {
        if (! empty($elements)) {
            ksort($elements);
            $allElements[$reflectionClass->getName()] = array_values($elements);
        }
        return $allElements;
    }
}
