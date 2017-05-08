<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

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
     * @return string[]
     */
    public function getOwnInterfaceNames(): array;

    /**
     * @return MethodReflectionInterface[]
     */
    public function getMethods(): array;

    /**
     * @return MethodReflectionInterface[]
     */
    public function getOwnMethods(): array;

    /**
     * @return MethodReflectionInterface[]
     */
    public function getInheritedMethods(): array;

    /**
     * @return MethodReflectionInterface[]
     */
    public function getUsedMethods(): array;

    /**
     * @return MethodReflectionInterface[]
     */
    public function getTraitMethods(): array;

    public function getMethod(string $name): MethodReflectionInterface;

    public function hasMethod(string $name): bool;

    /**
     * @return ConstantReflectionInterface[]
     */
    public function getOwnConstants(): array;

    /**
     * @return ConstantReflectionInterface[]
     */
    public function getInheritedConstants(): array;

    public function hasConstant(string $name): bool;

    public function getConstant(string $name): ClassConstantReflectionInterface;

    public function getOwnConstant(string $name): ClassConstantReflectionInterface;

    public function implementsInterface(string $interface): bool;
}
