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
	function getTypeHint();


	/**
	 * @return string
	 */
	function getDescription();


	/**
	 * @return string
	 */
	function getDefaultValueDefinition();


	/**
	 * @return bool
	 */
	function isDefaultValueAvailable();


	/**
	 * @return int
	 */
	function getPosition();


	/**
	 * @return bool
	 */
	function isArray();


	/**
	 * @return bool
	 */
	function isCallable();


	/**
	 * @return ClassReflectionInterface|NULL
	 */
	function getClass();


	/**
	 * @return string|NULL
	 */
	function getClassName();


	/**
	 * @return bool
	 */
	function allowsNull();


	/**
	 * @return bool
	 */
	function isOptional();


	/**
	 * @return bool
	 */
	function isPassedByReference();


	/**
	 * @return bool
	 */
	function canBePassedByValue();


	/**
	 * @return AbstractFunctionMethodReflectionInterface
	 */
	function getDeclaringFunction();


	/**
	 * @return string
	 */
	function getDeclaringFunctionName();


	/**
	 * @return ClassReflectionInterface|NULL
	 */
	function getDeclaringClass();


	/**
	 * @return string
	 */
	function getDeclaringClassName();


	/**
	 * @return bool
	 */
	function isUnlimited();

}
