<?php

/**
 * ApiGen - API Generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * This source file is subject to the "Nette license", and/or
 * GPL license. For more information please see http://nette.org
 */

namespace Apigen;
use TokenReflection\Broker\Backend\Memory, RuntimeException, ReflectionMethod;

class Backend extends Memory
{
	/**
	 * Prepares and returns used class lists.
	 *
	 * @return array
	 */
	protected function parseClassLists()
	{
		$declared = array_flip(array_merge(get_declared_classes(), get_declared_interfaces()));

		$allClasses = parent::parseClassLists();
		foreach ($allClasses[self::TOKENIZED_CLASSES] as $name => $class) {
			foreach ($class->getOwnMethods() as $method) {
				foreach (array('param', 'return', 'throws') as $annotation) {
					if (!$method->hasAnnotation($annotation)) {
						continue;
					}

					foreach ((array) $method->getAnnotation($annotation) as $doc) {
						foreach (explode('|', preg_replace('#\s.*#', '', $doc)) as $name) {
							$allClasses = $this->addClass($declared, $allClasses, $name);
						}
					}
				}

				foreach ($method->getParameters() as $param) {
					if ($hint = $param->getClass()) {
						$allClasses = $this->addClass(array(), $allClasses, $hint->getName());
					}
				}
			}

			foreach ($class->getOwnProperties() as $property) {
				if (!$property->hasAnnotation('var')) {
					continue;
				}

				foreach ((array) $property->getAnnotation('var') as $doc) {
					foreach (explode('|', preg_replace('#\s.*#', '', $doc)) as $name) {
						$allClasses = $this->addClass($declared, $allClasses, $name);
					}
				}
			}
		}

		return $allClasses;
	}

	/**
	 * Adds a class to list of classes.
	 *
	 * @param array $declared Array of declared classes (if empty, the class will be always added)
	 * @param array $allClasses Array with all classes parsed so far
	 * @param string $name Class name
	 * @return array
	 */
	private function addClass(array $declared, array $allClasses, $name)
	{
		$name = ltrim($name, '\\');

		if ((!empty($declared) && !isset($declared[$name])) || isset($allClasses[self::TOKENIZED_CLASSES][$name])  || isset($allClasses[self::INTERNAL_CLASSES][$name])) {
			return $allClasses;
		}

		$parameterClass = $this->getBroker()->getClass($name);
		if ($parameterClass->isTokenized()) {
			throw new RuntimeException(sprintf('Error. Trying to add a tokenized class %s. It should be already in the class list.', $name));
		} elseif ($parameterClass->isInternal()) {
			$allClasses[self::INTERNAL_CLASSES][$name] = $parameterClass;
			foreach (array_merge($parameterClass->getInterfaces(), $parameterClass->getParentClasses()) as $parentClass) {
				if (!isset($allClasses[self::INTERNAL_CLASSES][$parentName = $parentClass->getName()])) {
					$allClasses[self::INTERNAL_CLASSES][$parentName] = $parentClass;
				}
			}
		} else {
			$allClasses[self::NONEXISTENT_CLASSES][$name] = $parameterClass;
		}

		return $allClasses;
	}
}
