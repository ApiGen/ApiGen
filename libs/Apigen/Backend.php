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
use TokenReflection, TokenReflection\Broker\Backend\Memory, RuntimeException, ReflectionMethod;

/**
 * Customized TokenReflection broker backend.
 *
 * Adds internal classes from @param, @var, @return, @throws annotations as well
 * as parent classes to the overall class list.
 *
 * @author Ondřej Nešpor
 * @author Jaroslav Hanslík
 */
class Backend extends Memory
{
	/**
	 * Generator instance.
	 *
	 * @var \Apigen\Generator
	 */
	private $generator;

	/**
	 * Constructor.
	 *
	 * @param \Apigen\Generator $config Generator instance
	 */
	public function __construct(Generator $generator)
	{
		$this->generator = $generator;
	}

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
			$class = new Reflection($class, $this->generator);
			$allClasses[self::TOKENIZED_CLASSES][$name] = $class;
			if (!$class->isDocumented()) {
				continue;
			}

			foreach ($class->getOwnMethods() as $method) {
				$allClasses = $this->processFunction($declared, $allClasses, $method);
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

		foreach ($this->getFunctions() as $function) {
			$allClasses = $this->processFunction($declared, $allClasses, $function);
		}

		array_walk_recursive($allClasses, function(&$reflection, $name, Generator $generator) {
			if (!$reflection instanceof Reflection) {
				$reflection = new Reflection($reflection, $generator);
			}
		}, $this->generator);

		return $allClasses;
	}

	/**
	 * Processes a function/method and adds classes from annotations to the overall class array.
	 *
	 * @param array $declared Array of declared classes
	 * @param array $allClasses Array with all classes parsed so far
	 * @param TokenReflection\IReflectionFunctionBase $function Function/method reflection
	 * @return array
	 */
	private function processFunction(array $declared, array $allClasses, TokenReflection\IReflectionFunctionBase $function)
	{
		static $parsedAnnotations = array('param', 'return', 'throws');

		foreach ($parsedAnnotations as $annotation) {
			if (!$function->hasAnnotation($annotation)) {
				continue;
			}

			foreach ((array) $function->getAnnotation($annotation) as $doc) {
				foreach (explode('|', preg_replace('#\s.*#', '', $doc)) as $name) {
					$allClasses = $this->addClass($declared, $allClasses, $name);
				}
			}
		}

		foreach ($function->getParameters() as $param) {
			if ($hint = $param->getClass()) {
				$allClasses = $this->addClass($declared, $allClasses, $hint->getName());
			}
		}

		return $allClasses;
	}

	/**
	 * Adds a class to list of classes.
	 *
	 * @param array $declared Array of declared classes
	 * @param array $allClasses Array with all classes parsed so far
	 * @param string $name Class name
	 * @return array
	 */
	private function addClass(array $declared, array $allClasses, $name)
	{
		$name = ltrim($name, '\\');

		if (!isset($declared[$name]) || isset($allClasses[self::TOKENIZED_CLASSES][$name]) || isset($allClasses[self::INTERNAL_CLASSES][$name]) || isset($allClasses[self::NONEXISTENT_CLASSES][$name])) {
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

	/**
	 * Returns true if the given reflection should be documented, false if not.
	 *
	 * @param TokenReflection\IReflection $reflection Reflection object
	 * @return boolean
	 */
	private function filterReflections(TokenReflection\IReflection $reflection)
	{
		if (!$this->generator->getConfig()->deprecated && $reflection->isDeprecated()) {
			return false;
		}
		foreach ($this->generator->getConfig()->skipDocPath as $mask) {
			if (fnmatch($mask, $reflection->getFilename(), FNM_NOESCAPE | FNM_PATHNAME)) {
				return false;
			}
		}
		foreach ($this->generator->getConfig()->skipDocPrefix as $prefix) {
			if (0 === strpos($reflection->getName(), $prefix)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Returns all functions from all namespaces.
	 *
	 * @return array
	 */
	public function getFunctions()
	{
		return array_filter(parent::getFunctions(), array($this, 'filterReflections'));
	}

	/**
	 * Returns all constants from all namespaces.
	 *
	 * @return array
	 */
	public function getConstants()
	{
		return array_filter(parent::getConstants(), array($this, 'filterReflections'));
	}
}
