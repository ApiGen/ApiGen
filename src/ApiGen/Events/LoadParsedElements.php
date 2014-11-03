<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Parser\Parser;
use ApiGen\Parser\ParserStorage;
use Nette;
use Kdyby\Events\Subscriber;


class LoadParsedElements extends Nette\Object implements Subscriber
{

	/**
	 * @var ElementResolver
	 */
	private $elementResolver;
	/**
	 * @var ParserStorage
	 */
	private $parserStorage;


	public function __construct(ElementResolver $elementResolver, ParserStorage $parserStorage)
	{
		$this->elementResolver = $elementResolver;
		$this->parserStorage = $parserStorage;
	}


	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return array(
			'ApiGen\Parser\Parser::onParseFinish' => 'onFinish',
		);
	}


	public function onFinish($classes, $constants, $functions)
	{
		$this->parserStorage->setClasses($classes);
		$this->parserStorage->setConstants($constants);
		$this->parserStorage->setFunctions($functions);

		$this->elementResolver->setParsedClasses($classes);
		$this->elementResolver->setParsedConstants($constants);
		$this->elementResolver->setParsedFunctions($functions);
	}

}
