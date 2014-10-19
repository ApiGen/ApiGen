<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use Nette;


/**
 * Scans paths for files
 * @method array getSymlinks()
 */
interface Scanner
{

	/**
	 * Scans sources and return found files
	 *
	 * @param array $sources
	 * @param array $exclude
	 * @param array $extensions
	 * @return array
	 */
	function scan($sources, $exclude = array(), $extensions = array());

}
