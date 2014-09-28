<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\Generator\HtmlGenerator;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use Nette;
use Kdyby\Events\Subscriber;


class LoadRelativePathResolver extends Nette\Object implements Subscriber
{

	/**
	 * @var RelativePathResolver
	 */
	private $relativePathResolver;


	public function __construct(RelativePathResolver $relativePathResolver)
	{
		$this->relativePathResolver = $relativePathResolver;
	}


	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return array(
			'ApiGen\Generator\HtmlGenerator::onScanFinish' => 'onFinish',
		);
	}


	public function onFinish(HtmlGenerator $generator)
	{
		$this->relativePathResolver->setSymlinks($generator->getSymlinks());
	}

}
