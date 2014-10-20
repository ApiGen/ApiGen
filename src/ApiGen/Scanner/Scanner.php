<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Scanner;

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
	 * @param array $sources List of sources to be scanned (folder or files).
	 * @param array $exclude Excluded files.
	 * @param array $extensions File extensions to be scanned (e.g. php, phpt).
	 * @return array
	 */
	public function scan($sources, $exclude = array(), $extensions = array());

}
