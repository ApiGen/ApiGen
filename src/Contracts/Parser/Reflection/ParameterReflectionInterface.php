<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Parser\Reflection\TokenReflection\ReflectionInterface;

interface ParameterReflectionInterface extends ReflectionInterface
{

    /**
     * @return string
     */
    public function getTypeHint();


    /**
     * @return string
     */
    public function getDescription();


    /**
     * @return string
     */
    public function getDefaultValueDefinition();


    /**
     * @return bool
     */
    public function isDefaultValueAvailable();


    /**
     * @return int
     */
    public function getPosition();


    /**
     * @return bool
     */
    public function isArray();


    /**
     * @return bool
     */
    public function isCallable();


    /**
     * @return ClassReflectionInterface|NULL
     */
    public function getClass();


    /**
     * @return string|NULL
     */
    public function getClassName();


    /**
     * @return bool
     */
    public function allowsNull();


    /**
     * @return bool
     */
    public function isOptional();


    /**
     * @return bool
     */
    public function isPassedByReference();


    /**
     * @return bool
     */
    public function canBePassedByValue();


    /**
     * @return AbstractFunctionMethodReflectionInterface
     */
    public function getDeclaringFunction();


    /**
     * @return string
     */
    public function getDeclaringFunctionName();


    /**
     * @return ClassReflectionInterface|NULL
     */
    public function getDeclaringClass();


    /**
     * @return string
     */
    public function getDeclaringClassName();


    /**
     * @return bool
     */
    public function isUnlimited();
}
