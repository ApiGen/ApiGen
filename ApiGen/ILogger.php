<?php

/**
 * ApiGen 3.0dev - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

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
	 * Header placeholder.
	 *
	 * @var string
	 */
	const TYPE_HEADER = '%header';

	/**
	 * Option placeholder.
	 *
	 * @var string
	 */
	const TYPE_OPTION = '%option';

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
