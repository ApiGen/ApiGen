<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use InvalidArgumentException;
use TokenReflection\Exception\RuntimeException;
use TokenReflection\IReflectionParameter;


abstract class ReflectionFunctionBase extends ReflectionElement
{

	/**
	 * @var array
	 */
	protected $parameters;


	/**
	 * Returns the unqualified name
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
			$this->prepareParameters();

			$annotations = $this->getAnnotation('param');
			if ($annotations !== NULL) {
				foreach ($annotations as $position => $annotation) {
					if (isset($parameters[$position])) {
						// Standard parameter
						continue;
					}

					$annotationFormat = '~^(?:([\\w\\\\]+(?:\\|[\\w\\\\]+)*)\\s+)?\\$(\\w+),\\.{3}(?:\\s+(.*))?($)~s';
					if ( ! preg_match($annotationFormat, $annotation, $matches)) {
						// Wrong annotation format
						continue;
					}

					list(, $typeHint, $name) = $matches;

					if (empty($typeHint)) {
						$typeHint = 'mixed';
					}

					$parameter = $this->apiGenReflectionFactory->createParameterMagic()
						->setName($name)
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
	 * @param int|string $parameterName
	 * @return ReflectionParameter
	 * @throws InvalidArgumentException If there is no parameter of the given name|position.
	 */
	public function getParameter($parameterName)
	{
		$parameters = $this->getParameters();

		if (is_numeric($parameterName)) {
			if (isset($parameters[$parameterName])) {
				return $parameters[$parameterName];
			}

			throw new InvalidArgumentException(sprintf('There is no parameter at position "%d" in function/method "%s"',
				$parameterName, $this->getName()), RuntimeException::DOES_NOT_EXIST);

		} else {
			foreach ($parameters as $parameter) {
				if ($parameter->getName() === $parameterName) {
					return $parameter;
				}
			}

			throw new InvalidArgumentException(sprintf('There is no parameter "%s" in function/method "%s"',
				$parameterName, $this->getName()), RuntimeException::DOES_NOT_EXIST);
		}
	}


	/**
	 * @return int
	 */
	public function getNumberOfParameters()
	{
		return $this->reflection->getNumberOfParameters();
	}


	/**
	 * @return int
	 */
	public function getNumberOfRequiredParameters()
	{
		return $this->reflection->getNumberOfRequiredParameters();
	}


	private function prepareParameters()
	{
		$this->parameters = array_map(function (IReflectionParameter $parameter) {
			return $this->apiGenReflectionFactory->createFromReflection($parameter);
		}, $this->reflection->getParameters());
	}

}
