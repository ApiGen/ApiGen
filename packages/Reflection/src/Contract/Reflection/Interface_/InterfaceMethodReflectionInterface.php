<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Interface_;

use ApiGen\Reflection\Contract\Reflection\AbstractParameterReflectionInterface;

interface InterfaceMethodReflectionInterface extends AbstractInterfaceElementInterface
{
    public function getShortName(): string;

    /**
     * @return AbstractParameterReflectionInterface[]
     */
    public function getParameters(): array;
}
