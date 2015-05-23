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


interface PropertyReflectionInterface extends ElementReflectionInterface, InTraitInterface, InClassInterface,
	LinedInterface
{

	/**
	 * @return bool
	 */
	function isValid();


	/**
	 * @return bool
	 */
	function isDefault();


	/**
	 * @return bool
	 */
	function isStatic();


	/**
	 * @return mixed
	 */
	function getDefaultValue();


	/**
	 * @return string
	 */
	function getTypeHint();


	/**
	 * @return bool
	 */
	function isMagic();


	/**
	 * @return bool
	 */
	function isReadOnly();


	/**
	 * @return bool
	 */
	function isWriteOnly();


	/**
	 * @param string $name
	 * @return bool
	 */
	function hasAnnotation($name);


	/**
	 * @param string $name
	 * @return bool
	 */
	function getAnnotation($name);

}
