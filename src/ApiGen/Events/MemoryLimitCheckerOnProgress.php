<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\Console\ProgressBar;
use ApiGen\Metrics\MemoryLimitChecker;
use Nette;
use Kdyby\Events\Subscriber;


class MemoryLimitCheckerOnProgress extends Nette\Object implements Subscriber
{
	/**
	 * @var MemoryLimitChecker
	 */
	private $memoryLimitChecker;


	public function __construct(MemoryLimitChecker $memoryLimitChecker)
	{
		$this->memoryLimitChecker = $memoryLimitChecker;
	}


	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return array(
			'ApiGen\Generator\HtmlGenerator::onParseProgress' => 'onProgress',
			'ApiGen\Generator\HtmlGenerator::onGenerateProgress' => 'onProgress'
		);
	}


	public function onProgress()
	{
		$this->memoryLimitChecker->check();
	}

}
