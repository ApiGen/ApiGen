<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\MagicPropertyExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;

class MagicPropertyExtractor implements MagicPropertyExtractorInterface
{

    /**
     * {@inheritdoc}
     */
    public function extractFromClass(ClassReflectionInterface $reflectionClass)
    {
        $properties = [];
        if ($parentClass = $reflectionClass->getParentClass()) {
            $properties += $this->extractFromParentClass($parentClass, $reflectionClass->isDocumented());
        }

        if ($traits = $reflectionClass->getTraits()) {
            $properties += $this->extractFromTraits($traits, $reflectionClass->isDocumented());
        }
        return $properties;
    }


    /**
     * @param ClassReflectionInterface $parent
     * @param bool $isDocumented
     * @return MagicPropertyReflectionInterface[]
     */
    private function extractFromParentClass(ClassReflectionInterface $parent, $isDocumented)
    {
        $properties = [];
        while ($parent) {
            $properties = $this->extractOwnFromClass($parent, $isDocumented, $properties);
            $parent = $parent->getParentClass();
        }
        return $properties;
    }


    /**
     * @param array $traits
     * @param bool $isDocumented
     * @return MagicPropertyReflectionInterface[]
     */
    private function extractFromTraits($traits, $isDocumented)
    {
        $properties = [];
        foreach ($traits as $trait) {
            if (! $trait instanceof ClassReflectionInterface) {
                continue;
            }
            $properties = $this->extractOwnFromClass($trait, $isDocumented, $properties);
        }
        return $properties;
    }


    /**
     * @param ClassReflectionInterface $classReflection
     * @param bool $isDocumented
     * @param array $properties
     * @return MagicPropertyReflectionInterface[]
     */
    private function extractOwnFromClass(ClassReflectionInterface $classReflection, $isDocumented, array $properties)
    {
        foreach ($classReflection->getOwnMagicProperties() as $property) {
            if ($this->canBeExtracted($isDocumented, $properties, $property)) {
                $properties[$property->getName()] = $property;
            }
        }
        return $properties;
    }


    /**
     * @param bool $isDocumented
     * @param array $properties
     * @param MagicPropertyReflectionInterface $propertyReflection
     * @return bool
     */
    private function canBeExtracted(
        $isDocumented,
        array $properties,
        MagicPropertyReflectionInterface $propertyReflection
    ) {
        if (isset($properties[$propertyReflection->getName()])) {
            return false;
        }
        if ($isDocumented && ! $propertyReflection->isDocumented()) {
            return false;
        }
        return true;
    }
}
