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
	 * Generator.
	 *
	 * @var \Apigen\Generator
	 */
	protected static $generator = null;

	/**
	 * Config.
	 *
	 * @var \Apigen\Config
	 */
	protected static $config = null;

	/**
	 * Class methods cache.
	 *
	 * @var array
	 */
	private static $methods = array();

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
	private $isDocumented;

	/**
	 * Constructor.
	 *
	 * Sets the inspected element reflection.
	 *
	 * @param \TokenReflection\IReflection $reflection Inspected element reflection
	 * @param \Apigen\Generator $generator Apigen generator
	 */
	public function __construct(IReflection $reflection, Generator $generator)
	{
		if (null === self::$generator) {
			self::$generator = $generator;
			self::$config = $generator->getConfig();

			self::$methods = array_flip(get_class_methods($this));
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
		if (isset(self::$methods['get' . $key])) {
			return $this->{'get' . $key}();
		}

		if (isset(self::$methods['is' . $key])) {
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
		return isset(self::$methods['get' . $key]) || isset(self::$methods['is' . $key]) || $this->reflection->__isset($name);
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
	public function isDocumented()
	{
		if (null === $this->isDocumented) {
			if (self::$config->php && $this->reflection->isInternal()) {
				$this->isDocumented = true;
			} elseif (!$this->reflection->isTokenized()) {
				$this->isDocumented = false;
			} elseif (!self::$config->deprecated && $this->reflection->isDeprecated()) {
				$this->isDocumented = false;
			} elseif (!self::$config->internal && ($internal = $this->reflection->getAnnotation('internal')) && empty($internal[0])) {
				$this->isDocumented = false;
			} else {
				$this->isDocumented = true;
				foreach (self::$config->skipDocPath as $mask) {
					if (fnmatch($mask, $this->reflection->getFilename(), FNM_NOESCAPE | FNM_PATHNAME)) {
						$this->isDocumented = false;
						break;
					}
				}
				if (true === $this->isDocumented) {
					foreach (self::$config->skipDocPrefix as $prefix) {
						if (0 === strpos($this->reflection->getName(), $prefix)) {
							$this->isDocumented = false;
							break;
						}
					}
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
}
