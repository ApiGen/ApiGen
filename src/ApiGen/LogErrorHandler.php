<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen;

use Exception;


class LogErrorHandler implements ErrorHandler
{

	/**
	 * @var Logger
	 */
	private $logger;


	public function __construct(Logger $logger)
	{
		$this->logger = $logger;
	}


	public function handleException(Exception $e)
	{
		$this->logger->logException($e);
		exit(min(1, $e->getCode()));
	}

}
