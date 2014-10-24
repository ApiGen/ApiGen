<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen;

use Nette;


class Factory extends Nette\Object
{

	/**
	 * @return string
	 */
	public static function getApiGenFile()
	{
		return getcwd() . DS . 'apigen.neon';
	}

}
