<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\Generator\Generator;
use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Parser\Parser;
use ApiGen\Parser\ParserResult;
use Kdyby\Events\Subscriber;
use Nette;


class LoadParsedElements extends Nette\Object implements Subscriber
{

	/**
	 * @var ElementResolver
	 */
	private $elementResolver;

	/**
	 * @var Generator
	 */
	private $generator;


	public function __construct(ElementResolver $elementResolver, Generator $generator)
	{
		$this->elementResolver = $elementResolver;
		$this->generator = $generator;
	}


	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return ['ApiGen\Parser\Parser::onParseFinish' => 'onFinish'];
	}


	public function onFinish(Parser $parser)
	{
		ParserResult::$classes = $parser->getClasses();
		ParserResult::$constants = $parser->getConstants();
		ParserResult::$functions = $parser->getFunctions();

		$this->elementResolver->setParsedClasses($parser->getClasses());
		$this->elementResolver->setParsedConstants($parser->getConstants());
		$this->elementResolver->setParsedFunctions($parser->getFunctions());

		$this->generator->setParsedClasses($parser->getClasses());
		$this->generator->setParsedConstants($parser->getConstants());
		$this->generator->setParsedFunctions($parser->getFunctions());
	}

}
