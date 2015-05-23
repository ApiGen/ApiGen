<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Generator\Configuration;


interface GeneratorConfigurationInterface
{

	/**
	 * List of annotations.
	 *
	 * @return string[]
	 */
	function getAnnotationGroups();


	/**
	 * Group by namespaces or packages.
	 *
	 * @return string
	 */
	function getGroups();


	/**
	 * Get title of the project.
	 *
	 * @return string
	 */
	function getTitle();


	/**
	 * Base url of application.
	 *
	 * @var string
	 */
	function getBaseUrl();


	/**
	 * @return string
	 */
	function getGoogleCseId();


	/**
	 * @return bool
	 */
	function shouldGenerateSourceCode();


	/**
	 * @return string
	 */
	function getDestination();


	/**
	 * @return string
	 */
	function getZipFileName();


	/**
	 * @return bool
	 */
	function isAvailableForDownload();


	/**
	 * @return string[]
	 */
	function getSource();

}
