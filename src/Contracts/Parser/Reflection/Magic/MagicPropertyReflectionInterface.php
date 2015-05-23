<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection\Magic;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;


interface MagicPropertyReflectionInterface extends PropertyReflectionInterface
{

	/**
	 * @return bool
	 */
	function isDocumented();


	/**
	 * @return string
	 */
	function getShortDescription();


	/**
	 * @return string
	 */
	function getLongDescription();


	/**
	 * @return string
	 */
	function getDocComment();


	/**
	 * @return bool
	 */
	function isDeprecated();


	/**
	 * @return self
	 */
	function setDeclaringClass(ClassReflectionInterface $declaringClass);


	/**
	 * @return bool
	 */
	function isPrivate();


	/**
	 * @return bool
	 */
	function isProtected();


	/**
	 * @return bool
	 */
	function isPublic();


	/**
	 * @return string
	 */
	function getFileName();


	/**
	 * @return bool
	 */
	function isTokenized();

}
