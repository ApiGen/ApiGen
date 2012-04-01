<?php

namespace ApiGen;

use Exception;

/**
 * Logger interface.
 *
 * Processes messages during ApiGen run.
 */
interface ILogger
{
	/**
	 * Log a message.
	 *
	 * @param string $message Message
	 */
	public function log($message);

	/**
	 * Log en exception.
	 *
	 * @param \Exception $e Exception
	 */
	public function logException(Exception $e);
}
