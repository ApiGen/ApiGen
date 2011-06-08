<?php

/**
 * ApiGen - API Generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * This source file is subject to the "Nette license", and/or
 * GPL license. For more information please see http://nette.org
 */

namespace Apigen;

/**
 * Constant reflection envelope.
 *
 * Alters TokenReflection\IReflectionParameter functionality for ApiGen.
 *
 * @author Ondřej Nešpor
 */
class ReflectionParameter extends ReflectionBase
{
	/**
	 * Returns reflection of the required class of the value.
	 *
	 * @return \Apigen\ReflectionClass|null
	 */
	public function getClass()
	{
		$className = $this->reflection->getClassName();
		return null === $className ? null : self::$classes[$className];
	}

	/**
	 * Returns the declaring function.
	 *
	 * @return \TokenReflection\ReflectionFunctionBase
	 */
	public function getDeclaringFunction()
	{
		$functionName = $this->reflection->getDeclaringFunctionName();

		if ($className = $this->reflection->getDeclaringClassName()) {
			return self::$classes[$className]->getMethod($functionName);
		} else {
			return self::$functions[$functionName];
		}
	}

	/**
	 * Returns the method declaring class.
	 *
	 * @return \ApiGen\ReflectionClass|null
	 */
	public function getDeclaringClass()
	{
		$className = $this->reflection->getDeclaringClassName();
		return null === $className ? null : self::$classes[$className];
	}
}
