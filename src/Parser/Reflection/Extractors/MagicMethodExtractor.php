<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\MagicMethodExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;

class MagicMethodExtractor implements MagicMethodExtractorInterface
{

    public function extractFromClass(ClassReflectionInterface $reflectionClass)
    {
        $methods = [];

        if ($parentClass = $reflectionClass->getParentClass()) {
            $methods += $this->extractFromParentClass($parentClass, $reflectionClass->isDocumented());
        }

        if ($traits = $reflectionClass->getTraits()) {
            $methods += $this->extractFromTraits($traits, $reflectionClass->isDocumented());
        }
        return $methods;
    }


    /**
     * @param ClassReflectionInterface $parent
     * @param bool $isDocumented
     * @return MagicMethodReflectionInterface[]
     */
    private function extractFromParentClass(ClassReflectionInterface $parent, bool $isDocumented)
    {
        $methods = [];
        while ($parent) {
            $methods = $this->extractOwnFromClass($parent, $isDocumented, $methods);
            $parent = $parent->getParentClass();
        }
        return $methods;
    }


    /**
     * @param ClassReflectionInterface[] $traits
     * @param bool $isDocumented
     * @return MagicMethodReflectionInterface[]
     */
    private function extractFromTraits($traits, $isDocumented)
    {
        $methods = [];
        foreach ($traits as $trait) {
            if (! $trait instanceof ClassReflectionInterface) {
                continue;
            }
            $methods = $this->extractOwnFromClass($trait, $isDocumented, $methods);
        }
        return $methods;
    }


    /**
     * @param ClassReflectionInterface $reflectionClass
     * @param bool $isDocumented
     * @param array $methods
     * @return MagicMethodReflectionInterface[]
     */
    private function extractOwnFromClass(ClassReflectionInterface $reflectionClass, bool $isDocumented, array $methods)
    {
        foreach ($reflectionClass->getOwnMagicMethods() as $method) {
            if ($this->canBeExtracted($isDocumented, $methods, $method)) {
                $methods[$method->getName()] = $method;
            }
        }
        return $methods;
    }


    /**
     * @param bool $isDocumented
     * @param array $methods
     * @param MagicMethodReflectionInterface $methodReflection
     * @return bool
     */
    private function canBeExtracted(bool $isDocumented, array $methods, MagicMethodReflectionInterface $methodReflection): bool
    {
        if (isset($methods[$methodReflection->getName()])) {
            return false;
        }
        if ($isDocumented && ! $methodReflection->isDocumented()) {
            return false;
        }
        return true;
    }
}
