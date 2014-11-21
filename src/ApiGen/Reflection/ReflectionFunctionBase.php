<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use InvalidArgumentException;
use TokenReflection;


/**
 * Function/method reflection envelope parent class.
 * Alters TokenReflection\IReflectionFunctionBase functionality for ApiGen.
 */
abstract class ReflectionFunctionBase extends ReflectionElement
{

	/**
	 * @var array
	 */
	protected $parameters;


	/**
	 * Returns the unqualified name (UQN).
	 *
	 * @return string
	 */
	public function getShortName()
	{
		return $this->reflection->getShortName();
	}


	/**
	 * Returns if the function/method returns its value as reference.
	 *
	 * @return bool
	 */
	public function returnsReference()
	{
		return $this->reflection->returnsReference();
	}


	/**
	 * Returns a list of function/method parameters.
	 *
	 * @return ReflectionParameter[]
	 */
	public function getParameters()
	{
		if ($this->parameters === NULL) {
			$this->parameters = array_map(function (TokenReflection\IReflectionParameter $parameter) {
				return new ReflectionParameter($parameter);
			}, $this->reflection->getParameters());

			$annotations = $this->getAnnotation('param');
			if ($annotations !== NULL) {
				foreach ($annotations as $position => $annotation) {
					if (isset($parameters[$position])) {
						// Standard parameter
						continue;
					}

					$pattern = '~^(?:([\\w\\\\]+(?:\\|[\\w\\\\]+)*)\\s+)?\\$(\\w+),\\.{3}(?:\\s+(.*))?($)~s';
					if ( ! preg_match($pattern, $annotation, $matches)) {
						// Wrong annotation format
						continue;
					}

					list(, $typeHint, $name) = $matches;

					$parameter = new ReflectionParameterMagic(NULL);
					$parameter->setName($name)
						->setPosition($position)
						->setTypeHint($typeHint)
						->setDefaultValueDefinition(NULL)
						->setUnlimited(TRUE)
						->setPassedByReference(FALSE)
						->setDeclaringFunction($this);

					$this->parameters[$position] = $parameter;
				}
			}
		}

		return $this->parameters;
	}


	/**
	 * Returns a particular function/method parameter.
	 *
	 * @param integer|string $parameterName Parameter name or position
	 * @return ReflectionParameter
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

			throw new InvalidArgumentException(sprintf(
				'There is no parameter at position "%d" in function/method "%s"', $parameterName, $this->getName()
			), Exception\Runtime::DOES_NOT_EXIST);

		} else {
			foreach ($parameters as $parameter) {
				if ($parameter->getName() === $parameterName) {
					return $parameter;
				}
			}

			throw new InvalidArgumentException(sprintf(
				'There is no parameter "%s" in function/method "%s"', $parameterName, $this->getName()
			), Exception\Runtime::DOES_NOT_EXIST);
		}
	}


	/**
	 * Returns the number of parameters.
	 *
	 * @return integer
	 */
	public function getNumberOfParameters()
	{
		return $this->reflection->getNumberOfParameters();
	}


	/**
	 * Returns the number of required parameters.
	 *
	 * @return integer
	 */
	public function getNumberOfRequiredParameters()
	{
		return $this->reflection->getNumberOfRequiredParameters();
	}

}
