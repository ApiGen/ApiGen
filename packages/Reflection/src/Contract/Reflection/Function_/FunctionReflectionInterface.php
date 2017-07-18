<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Function_;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\InNamespaceInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;

interface FunctionReflectionInterface extends StartAndEndLineInterface, AnnotationsInterface,
    AbstractReflectionInterface, InNamespaceInterface
{
    public function returnsReference(): bool;

    /**
     * @return FunctionParameterReflectionInterface[]
     */
    public function getParameters(): array;

    public function getShortName(): string;

    public function getFileName(): ?string;
}
