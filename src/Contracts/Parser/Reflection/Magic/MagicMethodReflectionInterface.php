<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection\Magic;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\NamedInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;


interface MagicMethodReflectionInterface extends MethodReflectionInterface
{

	/**
	 * @return bool
	 */
	function isPublic();


	/**
	 * @return bool
	 */
	function isProtected();


	/**
	 * @return bool
	 */
	function isPrivate();


	/**
	 * @return MagicParameterReflectionInterface[]
	 */
	function getParameters();


	/**
	 * @param MagicParameterReflectionInterface[] $parameters
	 */
	function setParameters(array $parameters);


	/**
	 * @return string
	 */
	function getFileName();


	/**
	 * @return bool
	 */
	function isTokenized();

}
