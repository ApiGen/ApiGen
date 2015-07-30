<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

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
