<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen;

use Exception;


interface Logger
{

	/**
	 * Log a message.
	 *
	 * @param string $message
	 */
	public function log($message);


	/**
	 * Log en exception.
	 */
	public function logException(Exception $e);

}
