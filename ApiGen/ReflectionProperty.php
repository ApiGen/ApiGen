<?php

/**
 * ApiGen 2.1.0 - API documentation generator for PHP 5.3+
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
 * Alters TokenReflection\IReflectionProperty functionality for ApiGen.
 *
 * @author Ondřej Nešpor
 */
class ReflectionProperty extends ReflectionBase
{
	/**
	 * Returns the property declaring class.
	 *
	 * @return \ApiGen\ReflectionClass|null
	 */
	public function getDeclaringClass()
	{
		$className = $this->reflection->getDeclaringClassName();
		return null === $className ? null : self::$classes[$className];
	}

	/**
	 * Returns the property declaring trait.
	 *
	 * @return \ApiGen\ReflectionClass|null
	 */
	public function getDeclaringTrait()
	{
		$traitName = $this->reflection->getDeclaringTraitName();
		return null === $traitName ? null : self::$classes[$traitName];
	}
}
