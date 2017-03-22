<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\MagicPropertyExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;

final class MagicPropertyExtractor implements MagicPropertyExtractorInterface
{
    /**
     * @return MagicPropertyReflectionInterface[]
     */
    public function extractFromClass(ClassReflectionInterface $reflectionClass): array
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
     * @return MagicPropertyReflectionInterface[]
     */
    private function extractFromParentClass(ClassReflectionInterface $parent, bool $isDocumented): array
    {
        $properties = [];
        while ($parent) {
            $properties = $this->extractOwnFromClass($parent, $isDocumented, $properties);
            $parent = $parent->getParentClass();
        }

        return $properties;
    }


    /**
     * @param ClassReflectionInterface[] $traits
     * @return MagicPropertyReflectionInterface[]
     */
    private function extractFromTraits(array $traits, bool $isDocumented): array
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
     * @param mixed[] $properties
     * @return MagicPropertyReflectionInterface[]
     */
    private function extractOwnFromClass(
        ClassReflectionInterface $classReflection,
        bool $isDocumented,
        array $properties
    ): array {
        foreach ($classReflection->getOwnMagicProperties() as $property) {
            if ($this->canBeExtracted($isDocumented, $properties, $property)) {
                $properties[$property->getName()] = $property;
            }
        }

        return $properties;
    }


    /**
     * @param bool $isDocumented
     * @param PropertyReflectionInterface[] $properties
     * @param MagicPropertyReflectionInterface $propertyReflection
     */
    private function canBeExtracted(
        bool $isDocumented,
        array $properties,
        MagicPropertyReflectionInterface $propertyReflection
    ): bool {
        if (isset($properties[$propertyReflection->getName()])) {
            return false;
        }

        if ($isDocumented && ! $propertyReflection->isDocumented()) {
            return false;
        }

        return true;
    }
}
