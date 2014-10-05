<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Console;

use ApiGen\ApiGen;
use Kdyby;


class Application extends Kdyby\Console\Application
{

	/**
	 * {@inheritDoc}
	 */
	public function __construct()
	{
		parent::__construct('ApiGen', ApiGen::VERSION);
	}


	/**
	 * {@inheritDoc}
	 */
	public function getLongVersion()
	{
		return parent::getLongVersion() . ' ' . ApiGen::RELEASE_DATE;
	}


}
