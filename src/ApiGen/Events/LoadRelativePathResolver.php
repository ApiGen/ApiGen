<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Scanner\Scanner;
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
			'ApiGen\Scanner\PhpScanner::onScanFinish' => 'onFinish',
		);
	}


	public function onFinish(Scanner $scanner)
	{
		$this->relativePathResolver->setSymlinks($scanner->getSymlinks());
	}

}
