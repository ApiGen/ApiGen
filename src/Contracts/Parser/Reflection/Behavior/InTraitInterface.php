<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;

interface InTraitInterface extends InNamespaceInterface
{

    /**
     * @return ClassReflectionInterface
     */
    public function getDeclaringTrait();


    /**
     * @return string|NULL
     */
    public function getDeclaringTraitName();
}
