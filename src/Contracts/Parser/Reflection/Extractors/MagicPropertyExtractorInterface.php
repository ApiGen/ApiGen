<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;

interface MagicPropertyExtractorInterface
{

    /**
     * @return MagicPropertyReflectionInterface[]
     */
    public function extractFromClass(ClassReflectionInterface $reflectionClass);
}
