<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection\Magic;

use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;


interface MagicParameterReflectionInterface extends ParameterReflectionInterface
{

	/**
	 * @return string
	 */
	function getDocComment();


	/**
	 * @return int
	 */
	function getStartLine();


	/**
	 * @return int
	 */
	function getEndLine();


	/**
	 * @return string
	 */
	function getFileName();


	/**
	 * @return bool
	 */
	function isTokenized();

}
