<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\Charset\CharsetConvertor;
use ApiGen\Configuration\Configuration;
use ApiGen\Console;
use Kdyby\Events\Subscriber;
use Nette;


class OnOptionsResolve extends Nette\Object implements Subscriber
{

	/**
	 * @var CharsetConvertor
	 */
	private $charsetConvertor;


	public function __construct(CharsetConvertor $charsetConvertor)
	{
		$this->charsetConvertor = $charsetConvertor;
	}


	/**
	 * @return string[]
	 */
	public function getSubscribedEvents()
	{
		return array(
			'ApiGen\Configuration\Configuration::onOptionsResolve'
		);
	}


	public function onOptionsResolve(Configuration $configuration)
	{
		$charsets = $configuration->getOption('charsets');
		$this->charsetConvertor->setCharsets($charsets);
	}

}
