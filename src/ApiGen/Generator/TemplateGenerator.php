<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;


abstract class TemplateGenerator
{

	/**
	 * Generate template to file
	 */
	abstract public function generate();


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return TRUE;
	}

}
