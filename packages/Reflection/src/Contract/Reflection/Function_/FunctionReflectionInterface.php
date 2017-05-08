<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Function_;

use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;

interface FunctionReflectionInterface extends StartAndEndLineInterface, AnnotationsInterface
{
    public function returnsReference(): bool;

    /**
     * @return FunctionParameterReflectionInterface[]
     */
    public function getParameters(): array;

    public function getName(): string;

    public function getShortName(): string;

    public function isDeprecated(): bool;

    public function getNamespaceName(): string;

    public function getFileName(): string;
}
