<?php declare(strict_types=1);

namespace ApiGen\Element\Annotation;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;

final class SingleAnnotationStorage
{
    /**
     * @var string
     */
    private $annotation;

    /**
     * @var ClassReflectionInterface[]
     */
    private $classReflections;

    /**
     * @var InterfaceReflectionInterface[]
     */
    private $interfaceReflections;

    /**
     * @var TraitReflectionInterface[]
     */
    private $traitReflections;

    /**
     * @var FunctionReflectionInterface[]
     */
    private $functionReflections;

    /**
     * @var ClassReflectionInterface[]
     */
    private $classOrTraitMethodReflections;

    /**
     * @var ClassPropertyReflectionInterface[]|TraitPropertyReflectionInterface[]
     */
    private $classOrTraitPropertyReflections;

    /**
     * @var ClassReflectionInterface[]|InterfaceReflectionInterface[]
     */
    private $classOrInterfaceConstantReflections;

    /**
     * @param ClassReflectionInterface[] $classReflections
     * @param InterfaceReflectionInterface[] $interfaceReflections
     * @param TraitReflectionInterface[] $traitReflections
     * @param FunctionReflectionInterface[] $functionReflections
     * @param ClassReflectionInterface[] $classOrTraitMethodReflections
     * @param ClassPropertyReflectionInterface[]|TraitPropertyReflectionInterface[] $classOrTraitPropertyReflections
     * @param ClassReflectionInterface[]|InterfaceReflectionInterface[] $classOrInterfaceConstantReflections
     */
    public function __construct(
        string $annotation,
        array $classReflections,
        array $interfaceReflections,
        array $traitReflections,
        array $functionReflections,
        array $classOrTraitMethodReflections,
        array $classOrTraitPropertyReflections,
        array $classOrInterfaceConstantReflections
    ) {
        $this->annotation = $annotation;
        $this->classReflections = $classReflections;
        $this->interfaceReflections = $interfaceReflections;
        $this->traitReflections = $traitReflections;
        $this->functionReflections = $functionReflections;
        $this->classOrTraitMethodReflections = $classOrTraitMethodReflections;
        $this->classOrTraitPropertyReflections = $classOrTraitPropertyReflections;
        $this->classOrInterfaceConstantReflections = $classOrInterfaceConstantReflections;
    }

    /**
     * @return string
     */
    public function getAnnotation(): string
    {
        return $this->annotation;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassReflections(): array
    {
        return $this->classReflections;
    }

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaceReflections(): array
    {
        return $this->interfaceReflections;
    }

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraitReflections(): array
    {
        return $this->traitReflections;
    }

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctionReflections(): array
    {
        return $this->functionReflections;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassOrTraitMethodReflections(): array
    {
        return $this->classOrTraitMethodReflections;
    }

    /**
     * @return ClassPropertyReflectionInterface[]|TraitPropertyReflectionInterface[]
     */
    public function getClassOrTraitPropertyReflections(): array
    {
        return $this->classOrTraitPropertyReflections;
    }

    /**
     * @return ClassReflectionInterface[]|InterfaceReflectionInterface[]
     */
    public function getClassOrInterfaceConstantReflections(): array
    {
        return $this->classOrInterfaceConstantReflections;
    }

    public function hasAnyElements(): bool
    {
        return (bool)
            count($this->classReflections) +
            count($this->interfaceReflections) +
            count($this->traitReflections) +
            count($this->functionReflections) +
            count($this->classOrTraitMethodReflections) +
            count($this->classOrTraitPropertyReflections) +
            count($this->classOrInterfaceConstantReflections);
    }
}
