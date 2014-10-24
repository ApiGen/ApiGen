<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Parser;

use ArrayObject;
use Nette;


class ParserResult extends Nette\Object
{

	/**
	 * @var ArrayObject
	 */
	public static $classes;

	/**
	 * @var ArrayObject
	 */
	public static $constants;

	/**
	 * @var ArrayObject
	 */
	public static $functions;

}
