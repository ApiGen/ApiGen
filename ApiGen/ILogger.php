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
	 * Error placeholder.
	 *
	 * @var string
	 */
	const TYPE_ERROR = '%error';

	/**
	 * Number placeholder.
	 *
	 * @var string
	 */
	const TYPE_NUMBER = '%number';

	/**
	 * Value placeholder.
	 *
	 * @var string
	 */
	const TYPE_VALUE = '%value';

	/**
	 * Topmost header placeholder.
	 *
	 * @var string
	 */
	const TYPE_H1 = '%h1';

	/**
	 * Second header placeholder.
	 *
	 * @var string
	 */
	const TYPE_H2 = '%h2';

	/**
	 * Log a message.
	 *
	 * Takes one argument as the messasge format definition and variable number of other arguments
	 * that act like placeholder values (imagine sprintf).
	 *
	 * @param string $message Message format
	 * @param string $arg,...
	 */
	public function log($message);

	/**
	 * Log an exception.
	 *
	 * @param \Exception $e Exception
	 */
	public function logException(Exception $e);
}
