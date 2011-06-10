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
 * Function/method reflection envelope parent class.
 *
 * Alters TokenReflection\IReflectionFunctionBase functionality for ApiGen.
 *
 * @author Ondřej Nešpor
 */
abstract class ReflectionFunctionBase extends ReflectionBase
{
	/**
	 * Cache for list of parameters.
	 *
	 * @var array
	 */
	private $parameters;

	/**
	 * Returns a list of function/method parameters.
	 *
	 * @return array
	 */
	public function getParameters()
	{
		if (null === $this->parameters) {
			$this->parameters = array();
			foreach ($this->reflection->getParameters() as $parameter) {
				$this->parameters[$parameter->getName()] = new ReflectionParameter($parameter, self::$generator);
			}
		}

		return $this->parameters;
	}

	/**
	 * Returns a particular function/method parameter.
	 *
	 * @param integer|string $parameter Parameter name or position
	 * @return \TokenReflection\ReflectionParameter
	 * @throws \TokenReflection\Exception\Runtime If there is no parameter of the given name
	 * @throws \TokenReflection\Exception\Runtime If there is no parameter at the given position
	 */
	public function getParameter($parameter)
	{
		$parameters = $this->getParameters();

		if (is_numeric($parameter)) {
			if (isset($parameters[$parameter])) {
				return $parameters[$parameter];
			}

			throw new \InvalidArgumentException(sprintf('There is no parameter at position "%d" in function/method "%s".', $parameter, $this->getName()), Exception\Runtime::DOES_NOT_EXIST);
		} else {
			if (isset($this->parameters[$parameter])) {
				return $this->parameters[$parameter];
			}

			throw new \InvalidArgumentException(sprintf('There is no parameter "%s" in function/method "%s".', $parameter, $this->getName()), Exception\Runtime::DOES_NOT_EXIST);
		}
	}
}
