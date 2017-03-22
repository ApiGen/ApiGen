<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;

interface ClassMagicElementsExtractorInterface
{
    /**
     * @return MagicPropertyReflectionInterface[]
     */
    public function getMagicProperties(): array;


    /**
     * @return MagicPropertyReflectionInterface[]
     */
    public function getOwnMagicProperties(): array;


    /**
     * @return MagicMethodReflectionInterface[]
     */
    public function getInheritedMagicProperties(): array;


    /**
     * @return MagicMethodReflectionInterface[]
     */
    public function getUsedMagicProperties(): array;


    /**
     * @return MagicMethodReflectionInterface[]
     */
    public function getMagicMethods(): array;


    /**
     * @return MagicMethodReflectionInterface[]
     */
    public function getOwnMagicMethods(): array;


    /**
     * @return MagicMethodReflectionInterface[]
     */
    public function getUsedMagicMethods(): array;
}
