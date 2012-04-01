<?php

namespace ApiGen;

use Exception;

/**
 * Error handler interface.
 */
interface IErrorHandler
{
	/**
	 * Handles an exception.
	 *
	 * @param \Exception $e Thrown exception
	 */
	public function handleException(Exception $e);
}
