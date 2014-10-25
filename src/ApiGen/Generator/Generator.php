<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;


/**
 * @method Generator setConfig(array $config)
 */
interface Generator
{

	/**
	 * Generates API documentation.
	 */
	function generate();

}
