<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;

interface MagicMethodExtractorInterface
{
    /**
     * @return MagicMethodReflectionInterface[]
     */
    public function extractFromClass(ClassReflectionInterface $classReflection);
}
