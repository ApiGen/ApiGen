<?php declare(strict_types=1);

namespace ApiGen\Reflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;

final class ReflectionStorage
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
    public function addClassReflections(array $classReflections): void
    {
        array_walk($classReflections, function (ClassReflectionInterface $classReflection) {
        });
        sort($classReflections);
        foreach ($classReflections as $classReflection) {
            $this->classReflections[$classReflection->getName()] = $classReflection;
        }
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
    public function addInterfaceReflections(array $interfaceReflections): void
    {
        array_walk($interfaceReflections, function (InterfaceReflectionInterface $interfaceReflection) {
        });
        sort($interfaceReflections);
        foreach ($interfaceReflections as $interfaceReflection) {
            $this->interfaceReflections[$interfaceReflection->getName()] = $interfaceReflection;
        }
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
    public function addTraitReflections(array $traitReflections): void
    {
        array_walk($traitReflections, function (TraitReflectionInterface $traitReflection) {
        });
        sort($traitReflections);
        foreach ($traitReflections as $traitReflection) {
            $this->traitReflections[$traitReflection->getName()] = $traitReflection;
        }
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
        array_walk($functionReflections, function (FunctionReflectionInterface $functionReflection) {
        });
        sort($functionReflections);

        foreach ($functionReflections as $functionReflection) {
            $this->functionReflections[$functionReflection->getName()] = $functionReflection;
        }
    }
}
