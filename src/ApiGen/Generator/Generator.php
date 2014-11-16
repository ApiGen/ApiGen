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
 * @method Generator getConfig()
 */
interface Generator
{

	/**
	 * Generates API documentation.
	 */
	public function generate();


	/**
	 * Scans sources for PHP files.
	 *
	 * @param array $sources List of sources to be scanned (folder or files).
	 * @param array $exclude Excluded files.
	 * @param array $extensions File extensions to be scanned (e.g. php, phpt).
	 */
	public function scan($sources, $exclude = array(), $extensions = array());


	/**
	 * Parses PHP files.
	 *
	 * @return array
	 */
	public function parse();

}
