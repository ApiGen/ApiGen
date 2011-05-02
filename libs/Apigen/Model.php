<?php

/**
 * ApiGen - API Generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 *
 * This source file is subject to the "Nette license", and/or
 * GPL license. For more information please see http://nette.org
 */

namespace Apigen;

use NetteX,
	NetteX\Utils\Strings;



/**
 * Scans and reflects classes/interfaces structure.
 * @author     David Grudl
 */
class Model extends NetteX\Object
{
	/** @var string */
	private $dir;

	/** @var array or CustomClassReflection */
	private $classes;



	/**
	 * Scans and parses PHP files.
	 * @param  string  directory
	 * @return void
	 */
	public function parse($dir)
	{
		$robot = new NetteX\Loaders\RobotLoader;
		$robot->setCacheStorage(new NetteX\Caching\Storages\MemoryStorage);
		$robot->addDirectory($dir);
		$robot->register();

		// load add classes
		$this->dir = realpath($dir);
		$this->classes = array();
		foreach ($robot->getIndexedClasses() as $name => $foo) {
			$class = new CustomClassReflection($name);
			if (!$class->hasAnnotation('internal') && !$class->hasAnnotation('deprecated')) {
				$this->classes[$name] = $class;
			}
		}

		$robot->unregister();
	}



	/**
	 * Expands list of classes by internal classes and interfaces.
	 * @return void
	 */
	public function expand()
	{
		$found = array();
		foreach ($this->classes as $name => $class) {
			$found = array_merge($found, array_values(class_parents($name)));
			$found = array_merge($found, $class->getInterfaceNames());

			foreach ($class->getOwnMethods() as $method) {
				foreach ($method->getParameters() as $param) { // type hints
					try {
						if ($tmp = $param->getClass()) {
							$found[] = $tmp->getName();
						}
					} catch (\ReflectionException $e) {
					}
				}

				foreach (array('param', 'return', 'throws') as $annotation) {
				    if (isset($method->annotations[$annotation])) {
						foreach ($method->annotations[$annotation] as $doc) {
							$found = array_merge($found, self::splitAnnotation($doc));
						}
					}
				}
			}

			foreach ($class->getOwnProperties() as $property) {
				$found = array_merge($found, self::splitAnnotation($property->getAnnotation('var')));
			}
		}

		foreach ($found as $name) {
			if (!isset($this->classes[$name]) && (class_exists($name) || interface_exists($name))) {
				$class = new CustomClassReflection($name);
				if ($class->isInternal() || Strings::startsWith($class->getFileName(), $this->dir . DIRECTORY_SEPARATOR)) {
					$this->classes[$class->getName()] = $class;
				}
			}
		}
	}



	/** @return array or CustomClassReflection */
	public function getClasses()
	{
		return $this->classes;
	}



	/** @return string */
	public function getDirectory()
	{
		return $this->dir;
	}



	/**
	 * Tries to resolve type as class or interface name.
	 * @param  string
	 * @param  string
	 * @return string
	 */
	public function resolveType($type, $namespace = NULL)
	{
		if (substr($type, 0, 1) === '\\') {
			$namespace = '';
			$type = substr($type, 1);
		}
		return isset($this->classes["$namespace\\$type"]) ? "$namespace\\$type" : (isset($this->classes[$type]) ? $type : NULL);
	}



	/**
	 * Returns list of direct subclasses.
	 * @param  ReflectionClass
	 * @return array or CustomClassReflection
	 */
	public function getDirectSubClasses($parent)
	{
		$parent = $parent->getName();
		$res = array();
		foreach ($this->classes as $class) {
			if ($class->getParentClass() && $class->getParentClass()->getName() === $parent) {
				$res[$class->getName()] = $class;
			}
		}
		return $res;
	}



	/**
	 * Returns list of direct subclasses.
	 * @param  ReflectionClass
	 * @return array or CustomClassReflection
	 */
	public function getDirectImplementers($interface)
	{
		if (!$interface->isInterface()) return array();
		$interface = $interface->getName();
		$res = array();
		foreach ($this->classes as $class) {
			if (array_key_exists($interface, class_implements($class->getName()))) {
				if (!$class->getParentClass() ||
					!array_key_exists($interface, class_implements($class->getParentClass()->getName()))) {
					$res[$class->getName()] = $class;
				}
			}
		}
		return $res;
	}



	/**
	 * Splits to type|type|type and description.
	 * @return array
	 */
	public static function splitAnnotation($s, & $description = NULL)
	{
		list($types, $description) = preg_split('#\s+|$#', $s, 2);
		return explode('|', $types);
	}

}
