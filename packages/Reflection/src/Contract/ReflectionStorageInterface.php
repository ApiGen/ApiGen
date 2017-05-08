<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract;

use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\TraitReflectionInterface;
use ApiGen\Reflection\Reflection\InterfaceReflection;

interface ReflectionStorageInterface
{
    /**
     * @param ClassReflectionInterface[] $classReflections
     */
    public function setClassReflections(array $classReflections): void;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassReflections(): array;

    /**
     * @param InterfaceReflectionInterface[] $interfaceReflections
     */
    public function setInterfaceReflections(array $interfaceReflections): void;

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaceReflections(): array;

    /**
     * @param TraitReflectionInterface[] $traitReflections
     */
    public function testTraitReflections(array $traitReflections): void;

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraitReflections(): array;

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctionReflections(): array;
}
