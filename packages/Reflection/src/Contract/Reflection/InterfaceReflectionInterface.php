<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

use ApiGen\Reflection\Contract\TransformerCollectorInterface;

interface InterfaceReflectionInterface
{
    public function isDocumented(): bool;

    public function getParentClass(): ?ClassReflectionInterface;

    public function getParentClassName(): ?string;

    public function getFileName(): string;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getParentClasses(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectImplementers(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectImplementers(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getInterfaces(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getOwnInterfaces(): array;

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

    public function getConstant(string $name): ConstantReflectionInterface;

    public function getOwnConstant(string $name): ConstantReflectionInterface;

    public function getTransformerCollector(): TransformerCollectorInterface;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getParsedClasses(): array;

    public function isSubclassOf(string $class): bool;
}
