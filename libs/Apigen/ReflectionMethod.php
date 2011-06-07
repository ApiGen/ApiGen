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
 * Function reflection envelope.
 *
 * Alters TokenReflection\IReflectionMethod functionality for ApiGen.
 *
 * @author Ondřej Nešpor
 */
class ReflectionMethod extends ReflectionBase
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
