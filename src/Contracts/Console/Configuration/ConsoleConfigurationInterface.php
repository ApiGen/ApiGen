<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Console\Configuration;


interface ConsoleConfigurationInterface
{

	/**
	 * @return string
	 */
	function getDestination();


	/**
	 * Files or directories with the source code.
	 *
	 * @return string|array
	 */
	function getSource();


	/**
	 * Exclude masks for files/directories.
	 *
	 * @return string[]
	 */
	function getExclude();


	/**
	 * File extensions to be taken in account.
	 *
	 * @return string[]
	 */
	function getExtensions();


	/**
	 * Path to theme's assets files.
	 *
	 * @return array
	 */
	function getThemeResources();

}
