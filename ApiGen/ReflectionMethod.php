<?php

/**
 * TR ApiGen - API documentation generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
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
	 * Returns the method declaring trait.
	 *
	 * @return \ApiGen\ReflectionClass|null
	 */
	public function getDeclaringTrait()
	{
		$traitName = $this->reflection->getDeclaringTraitName();
		return null === $traitName ? null : self::$classes[$traitName];
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

	/**
	 * Returns the original method when importing from a trait.
	 *
	 * @return \ApiGen\ReflectionMethod|null
	 */
	public function getOriginal()
	{
		$originalName = $this->reflection->getOriginalName();
		return null === $originalName ? null : self::$classes[$this->reflection->getOriginal()->getDeclaringClassName()]->getMethod($originalName);
	}
}
