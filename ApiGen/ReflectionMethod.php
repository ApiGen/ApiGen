<?php

/**
 * ApiGen 2.6.1 - API documentation generator for PHP 5.3+
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
 * Method reflection envelope.
 *
 * Alters TokenReflection\IReflectionMethod functionality for ApiGen.
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
		return null === $className ? null : self::$parsedClasses[$className];
	}

	/**
	 * Returns the method declaring trait.
	 *
	 * @return \ApiGen\ReflectionClass|null
	 */
	public function getDeclaringTrait()
	{
		$traitName = $this->reflection->getDeclaringTraitName();
		return null === $traitName ? null : self::$parsedClasses[$traitName];
	}

	/**
	 * Returns the method prototype.
	 *
	 * @return \ApiGen\ReflectionMethod
	 */
	public function getPrototype()
	{
		$prototype = $this->reflection->getPrototype();
		return self::$parsedClasses[$prototype->getDeclaringClassName()]->getMethod($prototype->getName());
	}

	/**
	 * Returns the original method when importing from a trait.
	 *
	 * @return \ApiGen\ReflectionMethod|null
	 */
	public function getOriginal()
	{
		$originalName = $this->reflection->getOriginalName();
		return null === $originalName ? null : self::$parsedClasses[$this->reflection->getOriginal()->getDeclaringClassName()]->getMethod($originalName);
	}

	/**
	 * Returns if the method is valid.
	 *
	 * @return boolean
	 */
	public function isValid()
	{
		if ($class = $this->getDeclaringClass()) {
			return $class->isValid();
		}

		return true;
	}
}
