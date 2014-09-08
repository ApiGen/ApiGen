<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;


interface Generator
{

	/**
	 * Generates API documentation.
	 */
	public function generate();


	/**
	 * Scans sources for PHP files.
	 * @param array $sources
	 * @param array $exclude
	 * @return array
	 */
	public function scan($sources, $exclude = array());


	/**
	 * Parses PHP files.
	 * @return array
	 */
	public function parse();


	/**
	 * Wipes out the destination directory.
	 * @return boolean
	 */
	public function wipeOutDestination();


	/**
	 * Returns configuration
	 * @return Generator
	 */
	public function getConfig();

}
