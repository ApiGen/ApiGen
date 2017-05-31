<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Function_;

use ApiGen\Reflection\Contract\Reflection\AbstractParameterReflectionInterface;

interface FunctionParameterReflectionInterface extends AbstractParameterReflectionInterface
{
    public function getDeclaringFunction(): FunctionReflectionInterface;

    public function getDeclaringFunctionName(): string;

    public function getDescription(): string;
}
