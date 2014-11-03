<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use TokenReflection\IReflectionParameter;


/**
 * Parameter reflection envelope
 */
class ReflectionParameter extends ReflectionBase implements IReflectionParameter
{

	/**
	 * @return string
	 */
	public function getTypeHint()
	{
		if ($this->isArray()) {
			return 'array';

		} elseif ($this->isCallable()) {
			return 'callable';

		} elseif ($className = $this->getClassName()) {
			return $className;

		} elseif ($annotations = $this->getDeclaringFunction()->getAnnotation('param')) {
			if ( ! empty($annotations[$this->getPosition()])) {
				list($types) = preg_split('~\s+|$~', $annotations[$this->getPosition()], 2);
				if ( ! empty($types) && $types[0] !== '$') {
					return $types;
				}
			}
		}

		return 'mixed';
	}


	/**
	 * @return string
	 */
	public function getDescription()
	{
		$annotations = $this->getDeclaringFunction()->getAnnotation('param');
		if (empty($annotations[$this->getPosition()])) {
			return '';
		}

		$description = trim(strpbrk($annotations[$this->getPosition()], "\n\r\t "));
		return preg_replace('~^(\\$' . $this->getName() . '(?:,\\.{3})?)(\\s+|$)~i', '\\2', $description, 1);
	}


	/**
	 * Returns the part of the source code defining the parameter default value.
	 *
	 * @return string
	 */
	public function getDefaultValueDefinition()
	{
		return $this->reflection->getDefaultValueDefinition();
	}


	/**
	 * Retutns if a default value for the parameter is available.
	 *
	 * @return boolean
	 */
	public function isDefaultValueAvailable()
	{
		return $this->reflection->isDefaultValueAvailable();
	}


	/**
	 * Returns the position within all parameters.
	 *
	 * @return integer
	 */
	public function getPosition()
	{
		return $this->reflection->getPosition();
	}


	/**
	 * @return boolean
	 */
	public function isArray()
	{
		return $this->reflection->isArray();
	}


	/**
	 * @return boolean
	 */
	public function isCallable()
	{
		return $this->reflection->isCallable();
	}


	/**
	 * Returns reflection of the required class of the parameter.
	 *
	 * @return ReflectionClass|NULL
	 */
	public function getClass()
	{
		$className = $this->reflection->getClassName();
		$parsedClasses = $this->getParsedClasses();
		return $className === NULL ?: $parsedClasses[$className];
	}


	/**
	 * @return string|NULL
	 */
	public function getClassName()
	{
		return $this->reflection->getClassName();
	}


	/**
	 * Returns if the the parameter allows NULL.
	 *
	 * @return boolean
	 */
	public function allowsNull()
	{
		return $this->reflection->allowsNull();
	}


	/**
	 * @return boolean
	 */
	public function isOptional()
	{
		return $this->reflection->isOptional();
	}


	/**
	 * @return boolean
	 */
	public function isPassedByReference()
	{
		return $this->reflection->isPassedByReference();
	}


	/**
	 * @return boolean
	 */
	public function canBePassedByValue()
	{
		return $this->reflection->canBePassedByValue();
	}


	/**
	 * @return ReflectionFunctionBase
	 */
	public function getDeclaringFunction()
	{
		$functionName = $this->reflection->getDeclaringFunctionName();
		$parsedClasses = $this->getParsedClasses();
		if ($className = $this->reflection->getDeclaringClassName()) {
			return $parsedClasses[$className]->getMethod($functionName);

		} else {
			return $parsedClasses[$functionName];
		}
	}


	/**
	 * @return string
	 */
	public function getDeclaringFunctionName()
	{
		return $this->reflection->getDeclaringFunctionName();
	}


	/**
	 * Returns the function/method declaring class.
	 *
	 * @return ReflectionClass|NULL
	 */
	public function getDeclaringClass()
	{
		$className = $this->reflection->getDeclaringClassName();
		$parsedClasses = $this->getParsedClasses();
		return $className === NULL ?: $parsedClasses[$className];
	}


	/**
	 * @return string|NULL
	 */
	public function getDeclaringClassName()
	{
		return $this->reflection->getDeclaringClassName();
	}


	/**
	 * If the parameter can be used unlimited.
	 *
	 * @return boolean
	 */
	public function isUnlimited()
	{
		return FALSE;
	}


	public function getDocComment()
	{
		throw new \Exception('Not implemented nor required');
	}


	public function getDefaultValue()
	{
		throw new \Exception('Not implemented nor required');
	}


	public function isDefaultValueConstant()
	{
		throw new \Exception('Not implemented nor required');
	}


	public function getDefaultValueConstantName()
	{
		throw new \Exception('Not implemented nor required');
	}


	public function __toString()
	{
		throw new \Exception('Not implemented nor required');
	}

}
