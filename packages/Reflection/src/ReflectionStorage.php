<?php declare(strict_types=1);

namespace ApiGen\Reflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use Throwable;

final class ReflectionStorage
{
    /**
     * @var ClassReflectionInterface[]
     */
    private $classReflections = [];

    /**
     * @var ClassReflectionInterface[]
     */
    private $exceptionReflections = [];

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
     * @return ClassReflectionInterface[]
     */
    public function getExceptionReflections(): array
    {
        return $this->exceptionReflections;
    }

    /**
     * @param ClassReflectionInterface[] $classReflections
     */
    public function addClassReflections(array $classReflections): void
    {
        array_walk($classReflections, function (ClassReflectionInterface $classReflection): void {
        });
        sort($classReflections);
        foreach ($classReflections as $classReflection) {
            if ($classReflection->implementsInterface(Throwable::class)) {
                $this->exceptionReflections[$classReflection->getName()] = $classReflection;
            } else {
                $this->classReflections[$classReflection->getName()] = $classReflection;
            }
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
        array_walk($interfaceReflections, function (InterfaceReflectionInterface $interfaceReflection): void {
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
        array_walk($traitReflections, function (TraitReflectionInterface $traitReflection): void {
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
        array_walk($functionReflections, function (FunctionReflectionInterface $functionReflection): void {
        });
        sort($functionReflections);

        foreach ($functionReflections as $functionReflection) {
            $this->functionReflections[$functionReflection->getName()] = $functionReflection;
        }
    }

    public function getClass(string $name): ?ClassReflectionInterface
    {
        return $this->classReflections[$name] ?? $this->exceptionReflections[$name] ?? null;
    }

    public function getInterface(string $name): ?InterfaceReflectionInterface
    {
        return $this->interfaceReflections[$name] ?? null;
    }

    /**
     * @return ClassReflectionInterface|InterfaceReflectionInterface|null
     */
    public function getClassOrInterface(string $name)
    {
        $class = $this->getClass($name);

        return $class ?: $this->getInterface($name);
    }
}
