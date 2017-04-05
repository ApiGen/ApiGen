<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;

interface FunctionReflectionInterface extends AbstractFunctionMethodReflectionInterface, LinedInterface
{
    public function getFileName(): string;

    public function getPrettyName(): string;
}
