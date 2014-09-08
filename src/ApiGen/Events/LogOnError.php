<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\LogErrorHandler;
use Nette;
use Kdyby\Events\Subscriber;


class LogOnError extends Nette\Object implements Subscriber
{
	/**
	 * @var LogErrorHandler
	 */
	private $errorHandler;


	public function __construct(LogErrorHandler $errorHandler)
	{
		$this->errorHandler = $errorHandler;
	}


	public function getSubscribedEvents()
	{
		return array('ApiGen\Application\Application::onError');
	}


	public function onError(\Exception $e)
	{
		$this->errorHandler->handleException($e);
	}

}
