<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection\Extractors;

use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionPropertyMagic;


class MagicPropertyExtractor
{

	/**
	 * @param ReflectionClass $parent
	 * @param bool $isDocumented
	 * @return ReflectionPropertyMagic[]
	 */
	public function extractFromParentClass(ReflectionClass $parent, $isDocumented)
	{
		$properties = [];
		while ($parent) {
			$properties = $this->extractFromClass($parent, $isDocumented, $properties);
			$parent = $parent->getParentClass();
		}
		return $properties;
	}


	/**
	 * @param array $traits
	 * @param $isDocumented
	 * @return ReflectionPropertyMagic[]
	 */
	public function extractFromTraits($traits, $isDocumented)
	{
		$properties = [];
		foreach ($traits as $trait) {
			if ( ! $trait instanceof ReflectionClass) {
				continue;
			}
			$properties = $this->extractFromClass($trait, $isDocumented, $properties);
		}
		return $properties;
	}


	/**
	 * @param ReflectionClass $reflectionClass
	 * @param bool $isDocumented
	 * @param array $properties
	 * @return ReflectionPropertyMagic[]
	 */
	private function extractFromClass(ReflectionClass $reflectionClass, $isDocumented, array $properties)
	{
		foreach ($reflectionClass->getOwnMagicProperties() as $property) {
			if (isset($properties[$property->getName()])) {
				continue;
			}
			if ( ! $isDocumented || $property->isDocumented()) {
				$properties[$property->getName()] = $property;
			}
		}
		return $properties;
	}

}
