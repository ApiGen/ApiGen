<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Templating\Configuration;


interface TemplatingConfigurationInterface
{

	/**
	 * List of annotations.
	 *
	 * @return string[]
	 */
	function getAnnotationGroups();


	/**
	 * Is @internal annotation documented.
	 *
	 * @return bool
	 */
	function isInternalDocumented();


	/**
	 * Is documentation available for downloading.
	 *
	 * @return bool
	 */
	function isAvailableForDownload();


	/**
	 * Name of file with zipped documentation.
	 *
	 * @return string
	 */
	function getZipFileName();


	/**
	 * @return bool
	 */
	function isTreeAllowed();


	/**
	 * @return string
	 */
	function getDestination();

}
