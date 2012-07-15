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

use TokenReflection;
use InvalidArgumentException;

/**
 * Function/method reflection envelope parent class.
 *
 * Alters TokenReflection\IReflectionFunctionBase functionality for ApiGen.
 */
abstract class ReflectionFunctionBase extends ReflectionElement
{
	/**
	 * Returns a list of function/method parameters.
	 *
	 * @return array
	 */
	public function getParameters()
	{
		$generator = self::$generator;
		return array_map(function(TokenReflection\IReflectionParameter $parameter) use ($generator) {
			return new ReflectionParameter($parameter, $generator);
		}, $this->reflection->getParameters());
	}

	/**
	 * Returns a particular function/method parameter.
	 *
	 * @param integer|string $parameterName Parameter name or position
	 * @return \ApiGen\ReflectionParameter
	 * @throws \InvalidArgumentException If there is no parameter of the given name.
	 * @throws \InvalidArgumentException If there is no parameter at the given position.
	 */
	public function getParameter($parameterName)
	{
		$parameters = $this->getParameters();

		if (is_numeric($parameterName)) {
			if (isset($parameters[$parameterName])) {
				return $parameters[$parameterName];
			}

			throw new InvalidArgumentException(sprintf('There is no parameter at position "%d" in function/method "%s"', $parameterName, $this->getName()), Exception\Runtime::DOES_NOT_EXIST);
		} else {
			foreach ($parameters as $parameter) {
				if ($parameter->getName() === $parameterName) {
					return $parameter;
				}
			}

			throw new InvalidArgumentException(sprintf('There is no parameter "%s" in function/method "%s"', $parameterName, $this->getName()), Exception\Runtime::DOES_NOT_EXIST);
		}
	}
}
