<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;


interface ClassMagicElementsExtractorInterface
{

	/**
	 * @return MagicPropertyReflectionInterface[]
	 */
	function getMagicProperties();


	/**
	 * @return MagicPropertyReflectionInterface[]
	 */
	function getOwnMagicProperties();


	/**
	 * @return array {[ declaringClassName => MagicMethodReflectionInterface[] ]}
	 */
	function getInheritedMagicProperties();


	/**
	 * @return array {[ declaringClassName => MagicMethodReflectionInterface[] ]}
	 */
	function getUsedMagicProperties();


	/**
	 * @return MagicMethodReflectionInterface[]
	 */
	function getMagicMethods();


	/**
	 * @return MagicMethodReflectionInterface[]
	 */
	function getOwnMagicMethods();


	/**
	 * @return MagicMethodReflectionInterface[]
	 */
	function getUsedMagicMethods();

}
