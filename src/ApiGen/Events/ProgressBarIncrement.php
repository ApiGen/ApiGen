<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\Console\ProgressBar;
use Kdyby\Events\Subscriber;
use Nette;


class ProgressBarIncrement extends Nette\Object implements Subscriber
{

	/**
	 * @var ProgressBar
	 */
	private $progressBar;


	public function __construct(ProgressBar $progressBar)
	{
		$this->progressBar = $progressBar;
	}


	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return array(
			'ApiGen\Parser\Parser::onParseStart' => 'onStart',
			'ApiGen\Parser\Parser::onParseProgress' => 'onProgress',
			'ApiGen\Generator\HtmlGenerator::onGenerateStart' => 'onStart',
			'ApiGen\Generator\HtmlGenerator::onGenerateProgress' => 'onProgress'
		);
	}


	/**
	 * @param int $steps
	 */
	public function onStart($steps)
	{
		$this->progressBar->init($steps);
	}


	/**
	 * @param int $size
	 */
	public function onProgress($size)
	{
		$this->progressBar->increment($size);
	}

}
