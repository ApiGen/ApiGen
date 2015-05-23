<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Templating\Routing;

use ApiGen\Contracts\Parser\Reflection\Behavior\NamedInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;


interface TemplateRouterInterface
{

	/**
	 * @param string $name
	 * @return string
	 */
	function getNamespaceRoute($name);


	/**
	 * @param string $name
	 * @return string
	 */
	function getPackageRoute($name);


	/**
	 * @return string
	 */
	function getClassRoute(ClassReflectionInterface $classReflection);


	/**
	 * @return string
	 */
	function getConstantRoute(ConstantReflectionInterface $constantReflection);


	/**
	 * @return string
	 */
	function getFunctionRoute(FunctionReflectionInterface $functionReflection);


	/**
	 * @param string $name
	 * @return string
	 */
	function getAnnotationGroupRoute($name);


	/**
	 * @return string
	 */
	function getSourceCodeRoute(NamedInterface $element);


	/**
	 * @return string
	 */
	function getSourceCodeRouteWithAnchor(NamedInterface $element);

}
