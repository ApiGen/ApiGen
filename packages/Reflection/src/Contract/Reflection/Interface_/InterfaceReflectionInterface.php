<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Interface_;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;

interface InterfaceReflectionInterface extends StartAndEndLineInterface, AnnotationsInterface,
    AbstractReflectionInterface
{
    public function getNamespaceName(): string;

    public function getFileName(): string;

    /**
     * @return ClassReflectionInterface[]|InterfaceReflectionInterface[]
     */
    public function getImplementers(): array;

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
}
