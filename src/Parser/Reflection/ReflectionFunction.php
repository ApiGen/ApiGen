<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use TokenReflection;

class ReflectionFunction extends ReflectionFunctionBase implements FunctionReflectionInterface
{

    public function isValid(): bool
    {
        if ($this->reflection instanceof TokenReflection\Invalid\ReflectionFunction) {
            return false;
        }
        return true;
    }
}
