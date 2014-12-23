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
		if (isset($this->getParsedClasses()[$name])) {
			return $this->getParsedClasses()[$name];
		}
		return $this->reflectionFactory->createFromReflection($class);
	}


	/**
	 * @return array
	 */
	public function getClasses()
	{
		return array_map(function (IReflectionClass $class) {
			return isset($this->getParsedClasses()[$class->getName()])
				? $this->getParsedClasses()[$class->getName()]
				: $this->reflectionFactory->createFromReflection($class);
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
		return $constant === NULL ? NULL : $this->reflectionFactory->createFromReflection($constant);
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
			return $this->reflectionFactory->createFromReflection($constant);
		}, $this->reflection->getConstantReflections());
	}


	/**
	 * @param string $name
	 * @return ReflectionFunction|NULL
	 */
	public function getFunction($name)
	{
		$function = $this->reflection->getFunction($name);
		return $function === NULL ? NULL : $this->reflectionFactory->createFromReflection($function);
	}


	/**
	 * @return array
	 */
	public function getFunctions()
	{
		return array_map(function (IReflectionFunction $function) {
			return $this->reflectionFactory->createFromReflection($function);
		}, $this->reflection->getFunctions());
	}


	/**
	 * @return array
	 */
	public function getFunctionNames()
	{
		return $this->reflection->getFunctionNames();
	}

}
