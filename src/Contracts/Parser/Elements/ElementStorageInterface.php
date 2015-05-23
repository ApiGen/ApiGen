<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Elements;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;


interface ElementStorageInterface
{

	/**
	 * @return array
	 */
	function getNamespaces();


	/**
	 * @return array
	 */
	function getPackages();


	/**
	 * @return ClassReflectionInterface[]
	 */
	function getClasses();


	/**
	 * @return ClassReflectionInterface[]
	 */
	function getInterfaces();


	/**
	 * @return ClassReflectionInterface[]
	 */
	function getTraits();


	/**
	 * @return ClassReflectionInterface[]
	 */
	function getExceptions();


	/**
	 * @return ConstantReflectionInterface[]
	 */
	function getConstants();


	/**
	 * @return FunctionReflectionInterface[]
	 */
	function getFunctions();


	/**
	 * @return ClassReflectionInterface[]
	 */
	function getClassElements();


	/**
	 * @return array[]
	 */
	function getElements();

}
