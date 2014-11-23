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
		return new ReflectionClass($class);
	}


	/**
	 * @return array
	 */
	public function getClasses()
	{
		return array_map(function (IReflectionClass $class) {
			return isset(self::$parsedClasses[$class->getName()])
				? self::$parsedClasses[$class->getName()]
				: new ReflectionClass($class);
		}, $this->reflection->getClasses());
	}


	/**
	 * @param string $name
	 * @return ReflectionConstant|NULL
	 */
	public function getConstant($name)
	{
		return $this->getConstantReflection($name);
	}


	/**
	 * @param string $name
	 * @return ReflectionConstant|NULL
	 */
	public function getConstantReflection($name)
	{
		$constant = $this->reflection->getConstantReflection($name);
		return $constant === NULL ? NULL : new ReflectionConstant($constant);
	}


	/**
	 * @return array
	 */
	public function getConstants()
	{
		return $this->getConstantReflections();
	}


	/**
	 * @return array
	 */
	public function getConstantReflections()
	{
		return array_map(function (IReflectionConstant $constant) {
			return new ReflectionConstant($constant);
		}, $this->reflection->getConstantReflections());
	}


	/**
	 * @param string $name
	 * @return ReflectionFunction|NULL
	 */
	public function getFunction($name)
	{
		$function = $this->reflection->getFunction($name);
		return $function === NULL ? NULL : new ReflectionFunction($function);
	}


	/**
	 * Returns functions defined by this extension.
	 *
	 * @return array
	 */
	public function getFunctions()
	{
		return array_map(function (IReflectionFunction $function) {
			return new ReflectionFunction($function);
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
