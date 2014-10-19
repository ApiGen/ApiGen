<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\Console;
use ApiGen\Generator\HtmlGenerator;
use ApiGen\Parser\Broker\Backend;
use Kdyby\Events\Subscriber;
use Nette;


class SetGeneratorOnParseStart extends Nette\Object implements Subscriber
{

	/**
	 * @var Backend
	 */
	private $backend;


	public function __construct(Backend $backend)
	{
		$this->backend = $backend;
	}


	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return array(
			'ApiGen\Generator\HtmlGenerator::onParseStart'
		);
	}


	/**
	 * @param int $steps
	 * @param HtmlGenerator $generator
	 */
	public function onParseStart($steps, HtmlGenerator $generator)
	{
		$this->backend->setGenerator($generator);
	}

}
