<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Class_;

use ApiGen\Reflection\Contract\Reflection\AbstractMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;

interface ClassMethodReflectionInterface extends AbstractMethodReflectionInterface, AbstractClassElementInterface
{
    /**
     * @return MethodParameterReflectionInterface[]
     */
    public function getParameters(): array;

    public function getImplementedMethod(): ?InterfaceMethodReflectionInterface;

    /**
     * @return ClassMethodReflectionInterface|TraitMethodReflectionInterface|null
     */
    public function getOverriddenMethod();
}
