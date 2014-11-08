<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;


interface TemplateGenerator
{

	/**
	 * Generate template to file
	 */
	function generate();


	/**
	 * Optional condition for run
	 *
	 * @return bool
	 */
	function isAllowed();

}
