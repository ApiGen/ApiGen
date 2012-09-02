<?php

/**
 * ApiGen 2.7.0 - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen;

/**
 * Parameter reflection envelope.
 *
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
			if (!empty($annotations[$this->getPosition()])) {
				list($types) = preg_split('~\s+|$~', $annotations[$this->getPosition()], 2);
				if (!empty($types) && '$' !== $types[0]) {
					return $types;
				}
			}
		}

		return 'mixed';
	}

	/**
	 * Returns reflection of the required class of the parameter.
	 *
	 * @return \ApiGen\ReflectionClass|null
	 */
	public function getClass()
	{
		$className = $this->reflection->getClassName();
		return null === $className ? null : self::$parsedClasses[$className];
	}

	/**
	 * Returns the declaring function.
	 *
	 * @return \ApiGen\ReflectionFunctionBase
	 */
	public function getDeclaringFunction()
	{
		$functionName = $this->reflection->getDeclaringFunctionName();

		if ($className = $this->reflection->getDeclaringClassName()) {
			return self::$parsedClasses[$className]->getMethod($functionName);
		} else {
			return self::$parsedFunctions[$functionName];
		}
	}

	/**
	 * Returns the function/method declaring class.
	 *
	 * @return \ApiGen\ReflectionClass|null
	 */
	public function getDeclaringClass()
	{
		$className = $this->reflection->getDeclaringClassName();
		return null === $className ? null : self::$parsedClasses[$className];
	}

	/**
	 * If the parameter can be used unlimited.
	 *
	 * @return boolean
	 */
	public function isUnlimited()
	{
		return false;
	}
}
