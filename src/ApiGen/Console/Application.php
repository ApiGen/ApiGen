<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Console;

use ApiGen;
use Kdyby;


class Application extends Kdyby\Console\Application
{

	/**
	 * @param string $name
	 * @param string $version
	 */
	public function __construct($name = ApiGen\ApiGen::NAME, $version = ApiGen\ApiGen::VERSION)
	{
		parent::__construct($name, $version);
	}

}
