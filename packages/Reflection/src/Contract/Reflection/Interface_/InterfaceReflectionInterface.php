<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Interface_;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;

interface InterfaceReflectionInterface
{
    public function getName(): string;

    public function isDocumented(): bool;

    public function getFileName(): string;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectImplementers(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectImplementers(): array;

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaces(): array;

    /**
     * @return InterfaceMethodReflectionInterface[]
     */
    public function getMethods(): array;

    /**
     * @return InterfaceMethodReflectionInterface[]
     */
    public function getOwnMethods(): array;

    /**
     * @return InterfaceMethodReflectionInterface[]
     */
    public function getInheritedMethods(): array;

    public function getMethod(string $name): InterfaceMethodReflectionInterface;

    public function hasMethod(string $name): bool;

    /**
     * @return InterfaceConstantReflectionInterface[]
     */
    public function getOwnConstants(): array;

    /**
     * @return InterfaceConstantReflectionInterface[]
     */
    public function getInheritedConstants(): array;

    public function hasConstant(string $name): bool;

    public function getConstant(string $name): InterfaceConstantReflectionInterface;

    public function getOwnConstant(string $name): InterfaceConstantReflectionInterface;

    public function implementsInterface(string $interface): bool;

    public function getShortName(): string;

    public function getStartLine(): int;

    public function getEndLine(): int;
}
