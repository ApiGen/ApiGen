<?php

namespace ApiGen;

use Exception;

/**
 * Default error handling service.
 */
class ErrorHandler implements IErrorHandler
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
