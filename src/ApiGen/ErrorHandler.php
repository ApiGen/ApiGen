<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen;

use Exception;


interface ErrorHandler
{

	/**
	 * Handles an exception.
	 */
	public function handleException(Exception $e);

}
