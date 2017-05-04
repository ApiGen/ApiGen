<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\InterfaceReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TraitReflectionInterface;

interface ReflectionStorageInterface
{
    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassReflections(): array;

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaceReflections(): array;

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraitReflections(): array;

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctionReflections(): array;
}
