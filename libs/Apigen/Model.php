<?php

namespace Apigen;
use NetteX;

use Apigen\ClassEnvelope as CustomReflection;
use TokenReflection\Broker, TokenReflection\Broker\Backend;
use TokenReflection\IReflectionClass as ReflectionClass, TokenReflection\IReflectionProperty as ReflectionProperty, TokenReflection\IReflectionMethod as ReflectionMethod;

require_once __DIR__ . '/../TokenReflection/compressed.php';
require_once __DIR__ . '/ClassEnvelope.php';

/**
* Scans and reflects classes/interfaces structure.
*
* @author David Grudl
* @author Ondřej Nešpor
*/
class Model extends NetteX\Object
{
	/**
	 * Processed directory.
	 *
	 * @var string
	 */
	private $dir;

	/**
	 * Array of reflection envelopes.
	 *
	 * @var array
	 */
	private $classes = array();

	/**
	 * Scans and parses PHP files.
	 *
	 * @param string $dir Parsed directory
	 */
	public function parse($dir)
	{
		$this->dir = realpath($dir);

		$broker = new Broker(new Backend\Memory());
		$broker->processDirectory($dir);
		foreach ($broker->getClasses() as $name => $class) {
			if (!$class->isInternal()) {
				$this->classes[$name] = $class;
			}
		}
	}

	/**
	 * Expands the list of classes by internal classes and interfaces.
	 */
	public function expand()
	{
		$declared = array_flip(array_merge(get_declared_classes(), get_declared_interfaces()));

		foreach ($this->classes as $name => $class) {
			$this->classes[$name] = new CustomReflection($class);
			$this->addParents($class);

			foreach ($class->getOwnMethods() as $method) {
				foreach (array('param', 'return', 'throws') as $annotation) {
					if (!$method->hasAnnotation($annotation)) {
						continue;
					}

					foreach ((array) $method->getAnnotation($annotation) as $doc) {
						$types = preg_replace('#\s.*#', '', $doc);
						foreach (explode('|', $types) as $name) {
							$name = ltrim($name, '\\');
							if (!isset($this->classes[$name]) && isset($declared[$name])) {
								$parameterClass = $class->getBroker()->getClass($name);
								$this->classes[$name] = new CustomReflection($parameterClass);
								$this->addParents($parameterClass);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Adds class' parents to processed classes.
	 *
	 * @param ReflectionClass $class Class reflection
	 */
	private function addParents(ReflectionClass $class)
	{
		foreach (array_merge($class->getParentClassNameList(), $class->getInterfaceNames()) as $parent) {
			if (!isset($this->classes[$parent])) {
				$parentClass = $class->getBroker()->getClass($parent);
				$this->classes[$parent] = new CustomReflection($parentClass);
				$this->addParents($parentClass);
			}
		}
	}

	/**
	 * Returns a list of direct subclasses.
	 *
	 * @param ClassEnvelope Requested class
	 * @return array
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
	 * Returns a list of direct implementers.
	 *
	 * @param \TokenReflection\IReflectionClass
	 * @return array
	 */
	public function getDirectImplementers($interface)
	{
		if (!$interface->isInterface()) return array();

		$interface = $interface->getName();
		$res = array();
		foreach ($this->classes as $class) {
			if (!$class->implementsInterface($interface)) {
				continue;
			}

			$parent = $class->getParentClass();
			if (!$parent || !$parent->implementsInterface($interface)) {
				$res[$class->getName()] = $class;
			}
		}

		return $res;
	}

	/**
	 * Returns processed classes.
	 *
	 * @return array
	 */
	public function getClasses()
	{
		return $this->classes;
	}


	/**
	 * Returns the processed directory.
	 *
	 * @return string
	 */
	public function getDirectory()
	{
		return $this->dir;
	}

	/**
	 * Tries to resolve type as class or interface name.
	 *
	 * @param string Data type description
	 * @param string Namespace name
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
}
