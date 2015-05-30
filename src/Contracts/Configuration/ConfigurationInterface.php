<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Configuration;


interface ConfigurationInterface
{

	/**
	 * @return array
	 */
	function resolveOptions(array $options);


	/**
	 * @param string $name
	 * @return mixed|NULL
	 */
	function getOption($name);


	/**
	 * @return array
	 */
	function getOptions();


	function setOptions(array $options);


	/**
	 * Get property/method visibility level (public, protected or private, in binary code).
	 *
	 * @return int
	 */
	function getVisibilityLevel();


	/**
	 * Return name of main library
	 *
	 * @return string
	 */
	function getMain();


	/**
	 * Are PHP Core elements documented.
	 *
	 * @return bool
	 */
	function isPhpCoreDocumented();


	/**
	 * Are elements marked as "@internal" documented.
	 *
	 * @return bool
	 */
	function isInternalDocumented();


	/**
	 * Are elements marked as "@deprecated" documented.
	 *
	 * @return bool
	 */
	function isDeprecatedDocumented();


	/**
	 * Is grouping by namespaces enabled.
	 *
	 * @return bool
	 */
	function areNamespacesEnabled();


	/**
	 * Is grouping by packages enabled.
	 *
	 * @return bool
	 */
	function arePackagesEnabled();


	/**
	 * @return string
	 */
	function getZipFileName();


	/**
	 * List of annotations.
	 *
	 * @return string[]
	 */
	function getAnnotationGroups();


	/**
	 * Is documentation available for downloading.
	 *
	 * @return bool
	 */
	function isAvailableForDownload();


	/**
	 * @return bool
	 */
	function isTreeAllowed();


	/**
	 * @return string
	 */
	function getDestination();


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
	 * @return string[]
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

}
