<?php

/**
 * ApiGen 2.1 dev - API documentation generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen;

use TokenReflection\IReflection;

/**
 * Base reflection envelope.
 *
 * Alters TokenReflection\IReflection functionality for ApiGen.
 *
 * @author Jaroslav Hanslík
 * @author Ondřej Nešpor
 */
abstract class ReflectionBase
{
	/**
	 * List of classes.
	 *
	 * @var \ArrayObject
	 */
	protected static $classes;

	/**
	 * List of functions.
	 *
	 * @var \ArrayObject
	 */
	protected static $functions;

	/**
	 * Generator.
	 *
	 * @var \ApiGen\Generator
	 */
	protected static $generator = null;

	/**
	 * Config.
	 *
	 * @var \ApiGen\Config
	 */
	protected static $config = null;

	/**
	 * Class methods cache.
	 *
	 * @var array
	 */
	private static $reflectionMethods = array();

	/**
	 * Reflection type (reflection class).
	 *
	 * @var string
	 */
	private $reflectionType;

	/**
	 * Inspected class reflection.
	 *
	 * @var \TokenReflection\IReflectionClass
	 */
	protected $reflection;

	/**
	 * Cache for information if the class should be documented.
	 *
	 * @var boolean
	 */
	protected $isDocumented;

	/**
	 * Constructor.
	 *
	 * Sets the inspected element reflection.
	 *
	 * @param \TokenReflection\IReflection $reflection Inspected element reflection
	 * @param \ApiGen\Generator $generator ApiGen generator
	 */
	public function __construct(IReflection $reflection, Generator $generator)
	{
		if (null === self::$generator) {
			self::$generator = $generator;
			self::$config = $generator->getConfig();
			self::$classes = $generator->getClasses();
			self::$functions = $generator->getFunctions();
		}

		$this->reflectionType = get_class($this);
		if (!isset(self::$reflectionMethods[$this->reflectionType])) {
			self::$reflectionMethods[$this->reflectionType] = array_flip(get_class_methods($this));
		}

		$this->reflection = $reflection;
	}

	/**
	 * Retrieves a property or method value.
	 *
	 * First tries the envelope object's property storage, then its methods
	 * and finally the inspected element reflection.
	 *
	 * @param string $name Attribute name
	 * @return mixed
	 */
	public function __get($name)
	{
		$key = ucfirst($name);
		if (isset(self::$reflectionMethods[$this->reflectionType]['get' . $key])) {
			return $this->{'get' . $key}();
		}

		if (isset(self::$reflectionMethods[$this->reflectionType]['is' . $key])) {
			return $this->{'is' . $key}();
		}

		return $this->reflection->__get($name);
	}

	/**
	 * Checks if the given property exists.
	 *
	 * First tries the envelope object's property storage, then its methods
	 * and finally the inspected element reflection.
	 *
	 * @param mixed $name Property name
	 * @return boolean
	 */
	public function __isset($name)
	{
		$key = ucfirst($name);
		return isset(self::$reflectionMethods[$this->reflectionType]['get' . $key]) || isset(self::$reflectionMethods[$this->reflectionType]['is' . $key]) || $this->reflection->__isset($name);
	}

	/**
	 * Calls a method of the inspected element reflection.
	 *
	 * @param string $name Method name
	 * @param array $args Arguments
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		return call_user_func_array(array($this->reflection, $name), $args);
	}

	/**
	 * Returns if the class should be documented.
	 *
	 * @return boolean
	 */
	public function isMain()
	{
		return empty(self::$config->main) || 0 === strpos($this->reflection->getName(), self::$config->main);
	}

	/**
	 * Returns if the element should be documented.
	 *
	 * @return boolean
	 */
	public function isDocumented()
	{
		if (null === $this->isDocumented) {
			$this->isDocumented = $this->reflection->isTokenized() || $this->reflection->isInternal();

			if ($this->isDocumented) {
				if (!self::$config->php && $this->reflection->isInternal()) {
					$this->isDocumented = false;
				} elseif (!self::$config->deprecated && $this->reflection->isDeprecated()) {
					$this->isDocumented = false;
				} elseif (!self::$config->internal && ($internal = $this->reflection->getAnnotation('internal')) && empty($internal[0])) {
					$this->isDocumented = false;
				}
			}
		}

		return $this->isDocumented;
	}

	/**
	 * Returns element package name (including subpackage name).
	 *
	 * For internal elements returns "PHP", for elements in global space returns "None".
	 *
	 * @return string
	 */
	public function getPseudoPackageName()
	{
		if ($this->reflection->isInternal()) {
			return 'PHP';
		}

		if ($package = $this->reflection->getAnnotation('package')) {
			$packageName = preg_replace('~\s+.*~s', '', $package[0]);
			if ($subpackage = $this->reflection->getAnnotation('subpackage')) {
				$packageName .= '\\' . preg_replace('~\s+.*~s', '', $subpackage[0]);
			}
			return $packageName;
		}

		return 'None';
	}

	/**
	 * Returns element namespace name.
	 *
	 * For internal elements returns "PHP", for elements in global space returns "None".
	 *
	 * @return string
	 */
	public function getPseudoNamespaceName()
	{
		return $this->reflection->isInternal() ? 'PHP' : $this->reflection->getNamespaceName() ?: 'None';
	}
	
	/**
	 * filter:
	 * check for @api annotation
	 * @return bool 
	 */
	private function filterIsApi()
	{
		return $this->hasAnnotation('api');
	}
	
	/**
	 * filter:
	 * check if element documented
	 * @return bool
	 */
	private function filterIsDocumented()
	{
		return $this->isDocumented();
	}
	
	/**
	 * check if reflection passed all defined filters.
	 * 
	 * @throws Exception
	 * @return bool true if all filters passed, otherwise false
	 */
	public function checkFilter()
	{
		$filtersPassed = true;
		$mandatoryFilters = array('isDocumented');
		
		// get reflection type name
		$matches = array();
		preg_match('/.*Reflection(.*)/', $this->reflectionType, $matches);
		$reflectionTypeName = '';
		if (isset($matches[1])) {
			$reflectionTypeName = $matches[1];
		}
		if (!$reflectionTypeName)
		{
			throw new Exception('Class "'.$this->reflectionType.'" has not a reflection class name');
		}
		
		// get filter config name
		$filterConfigName = strtolower($reflectionTypeName).'Filter';
		$filter = self::$config->$filterConfigName;
		$filter = array_merge($filter, $mandatoryFilters);
		if($filter) {
			// check all filters
			foreach ($filter as $filterName) {
				$filterMethodName = 'filter'.ucfirst($filterName); 
				if (method_exists($this, $filterMethodName)) {
					$filtersPassed = $this->$filterMethodName();
					if (!$filtersPassed) {
						break;
					}
				} else {
					throw new Exception('Cant find filter method "'.$filterMethodName.'" in Class'.$this->reflectionType);
				}
			}
		}
		return $filtersPassed;
	}
}
