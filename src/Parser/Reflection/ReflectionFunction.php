<?php

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use TokenReflection;

class ReflectionFunction extends ReflectionFunctionBase implements FunctionReflectionInterface
{

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        if ($this->reflection instanceof TokenReflection\Invalid\ReflectionFunction) {
            return false;
        }
        return true;
    }
}
