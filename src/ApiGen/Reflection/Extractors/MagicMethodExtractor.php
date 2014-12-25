<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection\Extractors;

use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionMethodMagic;


class MagicMethodExtractor
{

	/**
	 * @param ReflectionClass $parent
	 * @param bool $isDocumented
	 * @return ReflectionMethodMagic[]
	 */
	public function extractFromParentClass(ReflectionClass $parent, $isDocumented)
	{
		$methods = [];
		while ($parent) {
			$methods = $this->extractFromClass($parent, $isDocumented, $methods);
			$parent = $parent->getParentClass();
		}
		return $methods;
	}


	/**
	 * @param array $traits
	 * @param $isDocumented
	 * @return ReflectionMethodMagic[]
	 */
	public function extractFromTraits($traits, $isDocumented)
	{
		$methods = [];
		foreach ($traits as $trait) {
			if ( ! $trait instanceof ReflectionClass) {
				continue;
			}
			$methods = $this->extractFromClass($trait, $isDocumented, $methods);
		}
		return $methods;
	}


	/**
	 * @param ReflectionClass $reflectionClass
	 * @param bool $isDocumented
	 * @param array $methods
	 * @return ReflectionMethodMagic[]
	 */
	private function extractFromClass(ReflectionClass $reflectionClass, $isDocumented, array $methods)
	{
		foreach ($reflectionClass->getOwnMagicMethods() as $method) {
			if (isset($methods[$method->getName()])) {
				continue;
			}
			if ( ! $isDocumented || $method->isDocumented()) {
				$methods[$method->getName()] = $method;
			}
		}
		return $methods;
	}

}
