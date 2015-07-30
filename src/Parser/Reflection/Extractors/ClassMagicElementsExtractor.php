<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\ClassMagicElementsExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;

class ClassMagicElementsExtractor implements ClassMagicElementsExtractorInterface
{

    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;

    /**
     * @var MagicPropertyReflectionInterface[]
     */
    private $ownMagicProperties;

    /**
     * @var MagicMethodReflectionInterface[]
     */
    private $ownMagicMethods;


    public function __construct(ClassReflectionInterface $classReflection)
    {
        $this->classReflection = $classReflection;
    }


    /**
     * {@inheritdoc}
     */
    public function getMagicProperties()
    {
        return $this->getOwnMagicProperties() + (new MagicPropertyExtractor)->extractFromClass($this->classReflection);
    }


    /**
     * {@inheritdoc}
     */
    public function getOwnMagicProperties()
    {
        if ($this->ownMagicProperties === null) {
            $this->ownMagicProperties = [];

            if ($this->classReflection->isVisibilityLevelPublic() && $this->classReflection->getDocComment()) {
                $extractor = new AnnotationPropertyExtractor($this->classReflection->getReflectionFactory());
                $this->ownMagicProperties += $extractor->extractFromReflection($this->classReflection);
            }
        }

        return $this->ownMagicProperties;
    }


    /**
     * {@inheritdoc}
     */
    public function getMagicMethods()
    {
        return $this->getOwnMagicMethods() + (new MagicMethodExtractor)->extractFromClass($this->classReflection);
    }


    /**
     * {@inheritdoc}
     */
    public function getOwnMagicMethods()
    {
        if ($this->ownMagicMethods === null) {
            $this->ownMagicMethods = [];

            if ($this->classReflection->isVisibilityLevelPublic() && $this->classReflection->getDocComment()) {
                $extractor = new AnnotationMethodExtractor($this->classReflection->getReflectionFactory());
                $this->ownMagicMethods += $extractor->extractFromReflection($this->classReflection);
            }
        }
        return $this->ownMagicMethods;
    }


    /**
     * {@inheritdoc}
     */
    public function getInheritedMagicProperties()
    {
        $properties = [];
        $allProperties = array_flip(array_map(function (PropertyReflectionInterface $property) {
            return $property->getName();
        }, $this->getOwnMagicProperties()));

        foreach ($this->classReflection->getParentClasses() as $class) {
            $inheritedProperties = $this->getUsedElements($class->getOwnMagicProperties(), $allProperties);
            $properties = $this->sortElements($inheritedProperties, $properties, $class);
        }

        return $properties;
    }


    /**
     * {@inheritdoc}
     */
    public function getInheritedMagicMethods()
    {
        $methods = [];
        $allMethods = array_flip(array_map(function (MethodReflectionInterface $method) {
            return $method->getName();
        }, $this->getOwnMagicMethods()));

        /** @var ClassReflectionInterface[] $parentClassesAndInterfaces */
        $parentClassesAndInterfaces = array_merge(
            $this->classReflection->getParentClasses(),
            $this->classReflection->getInterfaces()
        );
        foreach ($parentClassesAndInterfaces as $class) {
            $inheritedMethods = $this->getUsedElements($class->getOwnMagicMethods(), $allMethods);
            $methods = $this->sortElements($inheritedMethods, $methods, $class);
        }

        return $methods;
    }


    /**
     * {@inheritdoc}
     */
    public function getUsedMagicProperties()
    {
        $properties = [];
        $allProperties = array_flip(array_map(function (PropertyReflectionInterface $property) {
            return $property->getName();
        }, $this->getOwnMagicProperties()));

        foreach ($this->classReflection->getTraits() as $trait) {
            if (! $trait instanceof ClassReflectionInterface) {
                continue;
            }
            $usedProperties = $this->getUsedElements($trait->getOwnMagicProperties(), $allProperties);
            $properties = $this->sortElements($usedProperties, $properties, $trait);
        }

        return $properties;
    }


    /**
     * {@inheritdoc}
     */
    public function getUsedMagicMethods()
    {
        $usedMethods = [];
        foreach ($this->getMagicMethods() as $method) {
            $declaringTraitName = $method->getDeclaringTraitName();
            if ($declaringTraitName === null || $declaringTraitName === $this->classReflection->getName()) {
                continue;
            }
            $usedMethods[$declaringTraitName][$method->getName()]['method'] = $method;
        }
        return $usedMethods;
    }


    /**
     * @param ElementReflectionInterface[] $elementsToCheck
     * @param array $allElements
     * @return array
     */
    private function getUsedElements(array $elementsToCheck, array &$allElements)
    {
        $elements = [];
        foreach ($elementsToCheck as $property) {
            if (! array_key_exists($property->getName(), $allElements)) {
                $elements[$property->getName()] = $property;
                $allElements[$property->getName()] = null;
            }
        }
        return $elements;
    }


    /**
     * @return array
     */
    private function sortElements(array $elements, array $allElements, ClassReflectionInterface $classReflection)
    {
        if (! empty($elements)) {
            ksort($elements);
            $allElements[$classReflection->getName()] = array_values($elements);
        }
        return $allElements;
    }
}
