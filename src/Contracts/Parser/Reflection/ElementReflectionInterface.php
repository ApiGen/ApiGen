<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\NamedInterface;
use TokenReflection\Exception\BaseException;


interface ElementReflectionInterface extends NamedInterface
{

	/**
	 * @return bool
	 */
	function isMain();


	/**
	 * @return bool
	 */
	function isValid();


	/**
	 * @return bool
	 */
	function isDocumented();


	/**
	 * @return bool
	 */
	function isDeprecated();


	/**
	 * @return bool
	 */
	function inPackage();


	/**
	 * @return string
	 */
	function getPackageName();


	/**
	 * Returns element package name (including subpackage name).
	 * For internal elements returns "PHP", for elements in global space returns "None".
	 *
	 * @return string
	 */
	function getPseudoPackageName();


	/**
	 * @return bool
	 */
	function inNamespace();


	/**
	 * @return string
	 */
	function getNamespaceName();


	/**
	 * Returns element namespace name.
	 * For internal elements returns "PHP", for elements in global space returns "None".
	 *
	 * @return string
	 */
	function getPseudoNamespaceName();


	/**
	 * @return string[]
	 */
	function getNamespaceAliases();


	/**
	 * Returns reflection element annotations.
	 * Removes the short and long description.
	 * In case of classes, functions and constants, @package, @subpackage, @author and @license annotations
	 * are added from declaring files if not already present.
	 *
	 * @return array
	 */
	function getAnnotations();


	/**
	 * @param string $name
	 * @return array
	 */
	function getAnnotation($name);


	/**
	 * @param string $name
	 * @return bool
	 */
	function hasAnnotation($name);


	/**
	 * @param string $name
	 * @param mixed $value
	 * @return self
	 */
	function addAnnotation($name, $value);


	/**
	 * @return string
	 */
	function getShortDescription();


	/**
	 * @return string
	 */
	function getLongDescription();


	/**
	 * @return string|bool
	 */
	function getDocComment();


	/**
	 * @return string
	 */
	function getPrettyName();


	/**
	 * Returns the unqualified name (UQN).
	 *
	 * @return string
	 */
	function getShortName();


	/**
	 * @return int
	 */
	function getStartPosition();


	/**
	 * @return int
	 */
	function getEndPosition();


	/**
	 * @return self
	 */
	function addReason(BaseException $reason);


	/**
	 * @return BaseException[]
	 */
	function getReasons();


	/**
	 * @return bool
	 */
	function hasReasons();

}
