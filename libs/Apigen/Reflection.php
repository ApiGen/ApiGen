<?php

namespace Apigen;
use Apigen\Generator;
use TokenReflection\IReflectionClass, ReflectionMethod, ReflectionProperty;

/**
 * Class reflection envelope.
 */
class Reflection
{
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
	private $reflection;

	/**
	 * Apigen generator.
	 *
	 * @var \Apigen\Generator
	 */
	private $generator;

	/**
	 * Constructor.
	 *
	 * Sets the inspected class reflection.
	 *
	 * @param \TokenReflection\IReflectionClass $reflection Inspected class reflection
	 * @param \Apigen\Generator $generator Apigen generator
	 */
	public function __construct(IReflectionClass $reflection, Generator $generator) {
		if (empty(self::$methods)) {
			self::$methods = array_flip(get_class_methods($this));
		}

		$this->reflection = $reflection;
		$this->generator = $generator;
	}

	/**
	 * Magic __get method.
	 *
	 * First tries the envelope object's methods, than the inspected class reflection.
	 *
	 * @param string $name Attribute name
	 * @return mixed
	 */
	public function __get($name)
	{
		$key = ucfirst($name);
		if (isset(self::$methods['get' . $key])) {
			return $this->{'get' . $key}();
		} elseif (isset(self::$methods['is' . $key])) {
			return $this->{'is' . $key}();
		} else {
			return $this->reflection->__get($name);
		}
	}

	/**
	 * Calls a method of the inspected class reflection.
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
	 * Returns visible methods declared by inspected class.
	 *
	 * @return array
	 */
	public function getOwnMethods()
	{
		return $this->reflection->getOwnMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED);
	}

	/**
	 * Returns visible properties declared by inspected class.
	 *
	 * @return array
	 */
	public function getOwnProperties()
	{
		return $this->reflection->getOwnProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
	}

	/**
	 * Returns a parent class reflection encapsulated by this class.
	 *
	 * @return \Apigen\TokenReflectionCustomClassReflection
	 */
	public function getParentClass()
	{
		$classes = $this->generator->getClasses();
		if ($class = $this->reflection->getParentClassName()) {
			return isset($classes[$class]) ? $classes[$class] : new Reflection($this->getParentClass());

		}

		return $class;
	}

	/**
	 * Returns all parent classes reflections encapsulated by this class.
	 *
	 * @return array
	 */
	public function getParentClasses()
	{
		$classes = $this->generator->getClasses();

		return array_map(function($class) use($classes) {
			return isset($classes[$class->getName()]) ? $classes[$class->getName()] : new Reflection($class);
		}, $this->reflection->getParentClasses());
	}

	/**
	 * Returns all interface reflections encapsulated by this class.
	 *
	 * @return array
	 */
	public function getInterfaces()
	{
		$classes = $this->generator->getClasses();

		return array_map(function($class) use($classes) {
			return isset($classes[$class->getName()]) ? $classes[$class->getName()] : new Reflection($class);
		}, $this->reflection->getInterfaces());
	}

	/**
	 * Returns all interfaces implemented by the inspected class and not its parents.
	 *
	 * @return array
	 */
	public function getOwnInterfaces()
	{
		$classes = $this->generator->getClasses();

		return array_map(function($class) use($classes) {
			return isset($classes[$class->getName()]) ? $classes[$class->getName()] : new Reflection($class);
		}, $this->reflection->getOwnInterfaces(false));
	}

	/**
	 * Returns reflections of direct subclasses.
	 *
	 * @return array
	 */
	public function getDirectSubclasses()
	{
		$that = $this->name;
		return array_filter($this->generator->getClasses(), function(Reflection $class) use($that) {
			if (!$class->isSubclassOf($that)) {
				return false;
			}

			return null === $class->getParentClassName() || !$class->getParentClass()->isSubClassOf($that);
		});
	}

	/**
	 * Returns reflections of indirect subclasses.
	 *
	 * @return array
	 */
	public function getIndirectSubclasses()
	{
		$that = $this->name;
		return array_filter($this->generator->getClasses(), function(Reflection $class) use($that) {
			if (!$class->isSubclassOf($that)) {
				return false;
			}

			return null !== $class->getParentClassName() && $class->getParentClass()->isSubClassOf($that);
		});
	}

	/**
	 * Returns reflections of classes directly implementing this interface.
	 *
	 * @return array
	 */
	public function getDirectImplementers()
	{
		if (!$this->isInterface()) {
			return array();
		}

		$that = $this->name;
		return array_filter($this->generator->getClasses(), function(Reflection $class) use($that) {
			if (!$class->implementsInterface($that)) {
				return false;
			}

			return null === $class->getParentClassName() || !$class->getParentClass()->implementsInterface($that);
		});
	}

	/**
	 * Returns reflections of classes indirectly implementing this interface.
	 *
	 * @return array
	 */
	public function getIndirectImplementers()
	{
		if (!$this->isInterface()) {
			return array();
		}

		$that = $this->name;
		return array_filter($this->generator->getClasses(), function(Reflection $class) use($that) {
			if (!$class->implementsInterface($that)) {
				return false;
			}

			return null !== $class->getParentClassName() && $class->getParentClass()->implementsInterface($that);
		});
	}
}
