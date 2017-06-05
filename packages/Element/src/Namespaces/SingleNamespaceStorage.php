<?php declare(strict_types=1);

namespace ApiGen\Element\Namespaces;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;

final class SingleNamespaceStorage
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var ClassReflectionInterface[]
     */
    private $classReflections = [];

    /**
     * @var InterfaceReflectionInterface[]
     */
    private $interfaceReflections = [];

    /**
     * @var FunctionReflectionInterface[]
     */
    private $functionReflections = [];

    /**
     * @var TraitReflectionInterface[]
     */
    private $traitReflections = [];

    /**
     * @param ClassReflectionInterface[] $classReflections
     * @param InterfaceReflectionInterface[] $interfaceReflections
     * @param TraitReflectionInterface[] $traitReflections
     * @param FunctionReflectionInterface[] $functionReflections
     */
    public function __construct(
        string $namespace,
        array $classReflections,
        array $interfaceReflections,
        array $traitReflections,
        array $functionReflections
    ) {
        $this->namespace = $namespace;
        $this->classReflections = $classReflections;
        $this->interfaceReflections = $interfaceReflections;
        $this->traitReflections = $traitReflections;
        $this->functionReflections = $functionReflections;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
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
}
