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
 * Default error handling service.
 */
class ErrorHandler extends Object implements IErrorHandler
{
	/**
	 * Error handling service.
	 *
	 * @var \ApiGen\ILogger
	 */
	private $logger;

	/**
	 * Creates an error handling service.
	 *
	 * @param \ApiGen\ILogger $logger Logger service
	 */
	public function __construct(ILogger $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Handles an exception.
	 *
	 * @param \Exception $e Thrown exception
	 */
	public function handleException(Exception $e)
	{
		$this->logger->logException($e);
		exit(min(1, $e->getCode()));
	}
}
