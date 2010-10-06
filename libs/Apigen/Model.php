<?php

/**
 * API Generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 *
 * This source file is subject to the "Nette license", and/or
 * GPL license. For more information please see http://nette.org
 */

namespace Apigen;

use NetteX;



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
		$robot->setCacheStorage(new NetteX\Caching\MemoryStorage);
		$robot->addDirectory($dir);
		$robot->register();

		// load add classes
		$this->dir = realpath($dir);
		$this->classes = array();
		foreach ($robot->getIndexedClasses() as $name => $foo) {
			$class = new CustomClassReflection($name);
			if (!$class->hasAnnotation('internal')) {
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
		foreach ($this->classes as $name => $class) {
			foreach (array_merge(class_parents($name), $class->getInterfaceNames()) as $parent) {
				if (!isset($this->classes[$parent])) {
					$this->classes[$parent] = new CustomClassReflection($parent);
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
	 * Helpers for DocBlock extracting.
	 * @param  string
	 * @return string
	 */
	public static function extractDocBlock($doc)
	{
		$doc = trim($doc, '/*');
		$doc = preg_replace('#^\s*\**\s*(@var \S+\s*|@.*)#ms', '', $doc); // remove annotations
		$doc = preg_replace('#^\s*\** ?#m', '', $doc); // remove stars
		return NetteX\String::normalize(trim($doc));
	}

}
