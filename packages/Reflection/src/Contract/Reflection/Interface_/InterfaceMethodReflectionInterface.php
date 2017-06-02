<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Interface_;

use ApiGen\Reflection\Contract\Reflection\AbstractParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;

interface InterfaceMethodReflectionInterface extends AbstractInterfaceElementInterface, AnnotationsInterface
{
    public function getShortName(): string;

    /**
     * @return AbstractParameterReflectionInterface[]
     */
    public function getParameters(): array;
}
