<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AccessLevelInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;

interface AbstractMethodReflectionInterface extends StartAndEndLineInterface, AnnotationsInterface,
    AccessLevelInterface
{
    public function getName(): string;

    public function getShortName(): string;

    /**
     * @return AbstractParameterReflectionInterface[]
     */
    public function getParameters(): array;

    public function isAbstract(): bool;

    public function isFinal(): bool;

    public function isStatic(): bool;

    public function getImplementedMethod(): ?InterfaceMethodReflectionInterface;

    /**
     * @return ClassMethodReflectionInterface|TraitMethodReflectionInterface|null
     */
    public function getOverriddenMethod();

    public function returnsReference(): bool;
}
