<?php

namespace ApiGen;

/**
 * API documentation generator interface.
 */
interface IGenerator
{
	/**
	 * Generates API documentation.
	 */
	public function generate();

	/**
	 * Scans and parses PHP files.
	 *
	 * @return array
	 */
	public function parse();

	/**
	 * Wipes out the destination directory.
	 *
	 * @return boolean
	 */
	public function wipeOutDestination();
}
