<?php declare(strict_types=1);

namespace ApiGen\Reflection;

use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\TraitReflectionInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;

final class ReflectionStorage implements ReflectionStorageInterface
{
    /**
     * @var ClassReflectionInterface[]
     */
    private $classReflections = [];

    /**
     * @var InterfaceReflectionInterface[]
     */
    private $interfaceReflections = [];

    /**
     * @var TraitReflectionInterface[]
     */
    private $traitReflections = [];

    /**
     * @var FunctionReflectionInterface[]
     */
    private $functionReflections = [];

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassReflections(): array
    {
        return $this->classReflections;
    }

    /**
     * @param ClassReflectionInterface[] $classReflections
     */
    public function setClassReflections(array $classReflections): void
    {
        $this->classReflections = $classReflections;
    }

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaceReflections(): array
    {
        return $this->interfaceReflections;
    }

    /**
     * @param InterfaceReflectionInterface[] $interfaceReflections
     */
    public function setInterfaceReflections(array $interfaceReflections): void
    {
        $this->interfaceReflections = $interfaceReflections;
    }

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraitReflections(): array
    {
        return $this->traitReflections;
    }

    /**
     * @param TraitReflectionInterface[] $traitReflections
     */
    public function setTraitReflections(array $traitReflections): void
    {
        $this->traitReflections = $traitReflections;
    }

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctionReflections(): array
    {
        return $this->functionReflections;
    }

    /**
     * @param FunctionReflectionInterface[] $functionReflections
     */
    public function setFunctionReflections(array $functionReflections): void
    {
        $this->functionReflections = $functionReflections;
    }
}
