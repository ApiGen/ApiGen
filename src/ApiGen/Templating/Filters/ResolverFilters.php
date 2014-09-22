<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters;

use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Reflection\ReflectionClass;
use Nette;


class ResolverFilters extends Filters
{

	/**
	 * @var ElementResolver
	 */
	private $elementResolver;


	public function __construct(ElementResolver $elementResolver)
	{
		$this->elementResolver = $elementResolver;
	}


	/**
	 * @param string $className
	 * @param string|NULL $namespace
	 * @return ReflectionClass
	 */
	public function getClass($className, $namespace = NULL)
	{
		return $this->elementResolver->getClass($className, $namespace);
	}

}
