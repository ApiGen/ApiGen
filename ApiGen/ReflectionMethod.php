<?php

/**
 * TR ApiGen - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen;

/**
 * Function reflection envelope.
 *
 * Alters TokenReflection\IReflectionMethod functionality for ApiGen.
 *
 * @author Ondřej Nešpor
 */
class ReflectionMethod extends ReflectionFunctionBase
{
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

	/**
	 * Returns the method prototype.
	 *
	 * @return \Apigen\ReflectionMethod
	 */
	public function getPrototype()
	{
		$prototype = $this->reflection->getPrototype();
		return self::$classes[$prototype->getDeclaringClassName()]->getMethod($prototype->getName());
	}
}
