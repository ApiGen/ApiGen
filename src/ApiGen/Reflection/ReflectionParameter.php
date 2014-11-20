<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use ApiGen\Parser\ParserResult;
use ArrayObject;


/**
 * Parameter reflection envelope.
 * Alters TokenReflection\IReflectionParameter functionality for ApiGen.
 */
class ReflectionParameter extends ReflectionBase
{

	/**
	 * Returns parameter type hint.
	 *
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
	 * Returns parameter description.
	 *
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
	 * Returns if a default value for the parameter is available.
	 *
	 * @return bool
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
		return $this->reflection->position;
	}


	/**
	 * Returns if the parameter expects an array.
	 *
	 * @return bool
	 */
	public function isArray()
	{
		return $this->reflection->isArray();
	}


	/**
	 * Returns if the parameter expects a callback.
	 *
	 * @return bool
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
		return $className === NULL ? NULL : self::$parsedClasses[$className];
	}


	/**
	 * Returns the required class name of the value.
	 *
	 * @return string|NULL
	 */
	public function getClassName()
	{
		return $this->reflection->getClassName();
	}


	/**
	 * Returns if the the parameter allows NULL.
	 *
	 * @return bool
	 */
	public function allowsNull()
	{
		return $this->reflection->allowsNull();
	}


	/**
	 * Returns if the parameter is optional.
	 *
	 * @return bool
	 */
	public function isOptional()
	{
		return $this->reflection->isOptional();
	}


	/**
	 * Returns if the parameter value is passed by reference.
	 *
	 * @return bool
	 */
	public function isPassedByReference()
	{
		return $this->reflection->isPassedByReference();
	}


	/**
	 * Returns if the paramter value can be passed by value.
	 *
	 * @return bool
	 */
	public function canBePassedByValue()
	{
		return $this->reflection->canBePassedByValue();
	}


	/**
	 * Returns the declaring function.
	 *
	 * @return ReflectionFunctionBase
	 */
	public function getDeclaringFunction()
	{
		$functionName = $this->reflection->getDeclaringFunctionName();

		if ($className = $this->reflection->getDeclaringClassName()) {
			return self::$parsedClasses[$className]->getMethod($functionName);

		} else {
			$parsedFunctions = $this->getParsedFunctions();
			return $parsedFunctions[$functionName];
		}
	}


	/**
	 * Returns the declaring function name.
	 *
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
		return $className === NULL ? NULL : self::$parsedClasses[$className];
	}


	/**
	 * Returns the declaring class name.
	 *
	 * @return string|NULL
	 */
	public function getDeclaringClassName()
	{
		return $this->reflection->getDeclaringClassName();
	}


	/**
	 * If the parameter can be used unlimited.
	 *
	 * @return bool
	 */
	public function isUnlimited()
	{
		return FALSE;
	}


	/**
	 * @return ArrayObject
	 */
	private function getParsedFunctions()
	{
		if (self::$parsedFunctions === NULL) {
			self::$parsedFunctions = ParserResult::$functions;
		}
		return self::$parsedFunctions;
	}

}
