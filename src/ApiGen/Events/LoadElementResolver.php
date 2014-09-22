<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\Console\ProgressBar;
use ApiGen\Generator\Generator;
use ApiGen\Generator\HtmlGenerator;
use ApiGen\Generator\Resolvers\ElementResolver;
use Nette;
use Kdyby\Events\Subscriber;


class LoadElementResolver extends Nette\Object implements Subscriber
{

	/**
	 * @var ElementResolver
	 */
	private $elementResolver;


	public function __construct(ElementResolver $elementResolver)
	{
		$this->elementResolver = $elementResolver;
	}


	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return array(
			'ApiGen\Generator\HtmlGenerator::onParseFinish' => 'onFinish',
		);
	}


	public function onFinish(HtmlGenerator $generator)
	{
		$this->elementResolver->setParsedClasses($generator->getParsedClasses());
		$this->elementResolver->setParsedConstants($generator->getParsedConstants());
		$this->elementResolver->setParsedFunctions($generator->getParsedFunctions());
	}

}
