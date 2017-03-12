<?php

namespace ApiGen\Contracts\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;

interface AnnotationPropertyExtractorInterface
{

    /**
     * @return MagicPropertyReflectionInterface[]
     */
    public function extractFromReflection(ClassReflectionInterface $classReflection);
}
