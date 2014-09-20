<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use TokenReflection\IReflectionClass;
use TokenReflection\IReflectionConstant;
use TokenReflection\IReflectionFunction;


/**
 * Extension reflection envelope.
 * Alters TokenReflection\IReflectionExtension functionality for ApiGen.
 */
class ReflectionExtension extends ReflectionBase
{

	/**
	 * Returns a class reflection.
	 *
	 * @param string $name
	 * @return ReflectionClass|NULL
	 */
	public function getClass($name)
	{
		$class = $this->reflection->getClass($name);
		if ($class === NULL) {
			return NULL;
		}
		if (isset(self::$parsedClasses[$name])) {
			return self::$parsedClasses[$name];
		}
		return new ReflectionClass($class, self::$generator);
	}


	/**
	 * Returns classes defined by this extension.
	 *
	 * @return array
	 */
	public function getClasses()
	{
		$generator = self::$generator;
		$classes = self::$parsedClasses;
		return array_map(function (IReflectionClass $class) use ($generator, $classes) {
			return isset($classes[$class->getName()]) ? $classes[$class->getName()] : new ReflectionClass($class, $generator);
		}, $this->reflection->getClasses());
	}


	/**
	 * Returns a constant reflection.
	 *
	 * @param string $name
	 * @return ReflectionConstant|NULL
	 */
	public function getConstant($name)
	{
		return $this->getConstantReflection($name);
	}


	/**
	 * Returns a constant reflection.
	 *
	 * @param string $name
	 * @return ReflectionConstant|NULL
	 */
	public function getConstantReflection($name)
	{
		$constant = $this->reflection->getConstantReflection($name);
		return $constant === NULL ? NULL : new ReflectionConstant($constant, self::$generator);
	}


	/**
	 * Returns reflections of defined constants.
	 *
	 * @return array
	 */
	public function getConstants()
	{
		return $this->getConstantReflections();
	}


	/**
	 * Returns reflections of defined constants.
	 *
	 * @return array
	 */
	public function getConstantReflections()
	{
		$generator = self::$generator;
		return array_map(function (IReflectionConstant $constant) use ($generator) {
			return new ReflectionConstant($constant, $generator);
		}, $this->reflection->getConstantReflections());
	}


	/**
	 * Returns a function reflection.
	 *
	 * @param string $name Function name
	 * @return ReflectionFunction
	 */
	public function getFunction($name)
	{
		$function = $this->reflection->getFunction($name);
		return NULL === $function ? NULL : new ReflectionFunction($function, self::$generator);
	}


	/**
	 * Returns functions defined by this extension.
	 *
	 * @return array
	 */
	public function getFunctions()
	{
		$generator = self::$generator;
		return array_map(function (IReflectionFunction $function) use ($generator) {
			return new ReflectionFunction($function, $generator);
		}, $this->reflection->getFunctions());
	}


	/**
	 * Returns names of functions defined by this extension.
	 *
	 * @return array
	 */
	public function getFunctionNames()
	{
		return $this->reflection->getFunctionNames();
	}

}
