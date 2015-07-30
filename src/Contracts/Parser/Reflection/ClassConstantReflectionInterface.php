<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;

interface ClassConstantReflectionInterface extends ElementReflectionInterface, InClassInterface, LinedInterface
{

    /**
     * @return string
     */
    public function getTypeHint();


    /**
     * @return mixed
     */
    public function getValue();


    /**
     * @return string
     */
    public function getValueDefinition();
}
