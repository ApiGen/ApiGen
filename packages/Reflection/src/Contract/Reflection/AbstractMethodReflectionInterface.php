<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;

interface AbstractMethodReflectionInterface extends StartAndEndLineInterface
{
    public function getNamespaceName(): string;

    public function isAbstract(): bool;

    public function isFinal(): bool;

    public function isStatic(): bool;

    public function getImplementedMethod(): ?InterfaceMethodReflectionInterface;

    /**
     * @return ClassMethodReflectionInterface|TraitMethodReflectionInterface|null
     */
    public function getOverriddenMethod();

    public function returnsReference(): bool;

    /**
     * @return AbstractParameterReflectionInterface[]
     */
    public function getParameters(): array;

    public function getName(): string;

    /**
     * Returns the unqualified name (UQN).
     */
    public function getShortName(): string;

    public function isDocumented(): bool;

    public function isDeprecated(): bool;

    /**
     *
     * @return mixed[]
     */
    public function getAnnotations(): array;

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array;

    public function hasAnnotation(string $name): bool;

    public function getDescription(): string;
}
