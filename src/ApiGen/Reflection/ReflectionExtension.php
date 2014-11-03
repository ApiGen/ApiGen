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
use TokenReflection\IReflectionExtension;
use TokenReflection\IReflectionFunction;


/**
 * Extension reflection envelope
 */
class ReflectionExtension extends ReflectionBase implements IReflectionExtension
{

	/**
	 * @param string $name
	 * @return ReflectionClass
	 */
	public function getClass($name)
	{
		$class = $this->reflection->getClass($name);
		if ($class === NULL) {
			return NULL;
		}

		$parsedClasses = $this->getParsedClasses();
		if (isset($parsedClasses[$name])) {
			return $parsedClasses[$name];
		}

		return $this->apiGenReflectionFactory->createFromReflection($class);
	}


	/**
	 * @return array
	 */
	public function getClasses()
	{
		$classes = $this->getParsedClasses();
		return array_map(function (IReflectionClass $class) use ($classes) {
			return isset($classes[$class->getName()]) ? $classes[$class->getName()] : new ReflectionClass($class);
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
		if ($constant = $this->reflection->getConstantReflection($name)) {
			return $this->apiGenReflectionFactory->createFromReflection($constant);
		}

		return NULL;
	}


	/**
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
		return array_map(function (IReflectionConstant $constant) {
			return $this->apiGenReflectionFactory->createFromReflection($constant);
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
		if ($function = $this->reflection->getFunction($name)) {
			return $this->apiGenReflectionFactory->createFromReflection($function);
		}
		return NULL;
	}


	/**
	 * Returns functions defined by this extension.
	 *
	 * @return array
	 */
	public function getFunctions()
	{
		return array_map(function (IReflectionFunction $function) {
			return $this->apiGenReflectionFactory->createFromReflection($function);
		}, $this->reflection->getFunctions());
	}


	/**
	 * @return array
	 */
	public function getFunctionNames()
	{
		return $this->reflection->getFunctionNames();
	}


	/**
	 * Returns class names defined by this extension.
	 *
	 * @return array
	 */
	public function getClassNames()
	{
		// TODO: Implement getClassNames() method.
	}


	/**
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->getName();
	}

}
