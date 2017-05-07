<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;

interface FunctionParameterReflectionInterface extends AbstractParameterReflectionInterface
{
    public function getDeclaringFunction(): FunctionReflectionInterface;

    public function getDeclaringFunctionName(): string;
}
