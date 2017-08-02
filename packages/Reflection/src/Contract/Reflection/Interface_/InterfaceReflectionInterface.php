<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Interface_;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\FileNameAwareReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\InNamespaceInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;

interface InterfaceReflectionInterface extends StartAndEndLineInterface, AnnotationsInterface, AbstractReflectionInterface, InNamespaceInterface, FileNameAwareReflectionInterface
{
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

    public function getMethod(string $name): InterfaceMethodReflectionInterface;

    /**
     * @return InterfaceConstantReflectionInterface[]
     */
    public function getOwnConstants(): array;

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getOwnInterfaces(): array;

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
