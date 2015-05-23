<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Configuration;


interface ParserConfigurationInterface
{

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

}
