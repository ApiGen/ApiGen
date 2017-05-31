<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Class_;

use ApiGen\Reflection\Contract\Reflection\AbstractMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;

interface ClassMethodReflectionInterface extends AbstractMethodReflectionInterface, AbstractClassElementInterface
{
    /**
     * @return MethodParameterReflectionInterface[]
     */
    public function getParameters(): array;
}
