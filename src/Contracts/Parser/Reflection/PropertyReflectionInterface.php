<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\InTraitInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\NamedInterface;

interface PropertyReflectionInterface extends
    ElementReflectionInterface,
    InTraitInterface,
    InClassInterface,
    LinedInterface
{

    /**
     * @return bool
     */
    public function isValid();


    /**
     * @return bool
     */
    public function isDefault();


    /**
     * @return bool
     */
    public function isStatic();


    /**
     * @return mixed
     */
    public function getDefaultValue();


    /**
     * @return string
     */
    public function getTypeHint();


    /**
     * @return bool
     */
    public function isMagic();


    /**
     * @return bool
     */
    public function isReadOnly();


    /**
     * @return bool
     */
    public function isWriteOnly();


    /**
     * @param string $name
     * @return bool
     */
    public function hasAnnotation($name);


    /**
     * @param string $name
     * @return bool
     */
    public function getAnnotation($name);
}
