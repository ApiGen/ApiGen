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

/**
 * Envelope for parameters that can be used unlimited times
 * and are not defined in function/method signature, only in @param annotation.
 */
class ReflectionParameterUnlimited extends ReflectionBase
{
	/**
	 * Parameter name.
	 *
	 * @var string
	 */
	 protected $name;

	/**
	 * Defines a type hint of parameter values.
	 *
	 * @var string
	 */
	protected $typeHint;

	/**
	 * Position of the parameter in the function/method.
	 *
	 * @var integer
	 */
	protected $position;

	/**
	 * The declaring function.
	 *
	 * @var \ApiGen\ReflectionFunctionBase
	 */
	protected $declaringFunction;

	/**
	 * Constructor.
	 *
	 * @param \TokenReflection\IReflection $reflection Inspected reflection
	 * @param \ApiGen\Generator $generator ApiGen generator
	 */
	public function __construct(IReflection $reflection = null, Generator $generator = null)
	{
		$this->reflectionType = get_class($this);
		if (!isset(self::$reflectionMethods[$this->reflectionType])) {
			self::$reflectionMethods[$this->reflectionType] = array_flip(get_class_methods($this));
		}
	}

	/**
	 * Sets parameter name.
	 *
	 * @param string $name
	 * @return \Apigen\ReflectionParameterUnlimited
	 */
	public function setName($name)
	{
		$this->name = (string) $name;
		return $this;
	}

	/**
	 * Sets type hint.
	 *
	 * @param string $typeHint
	 * @return \ApiGen\ReflectionParameterUnlimited
	 */
	public function setTypeHint($typeHint)
	{
		$this->typeHint = (string) $typeHint;
		return $this;
	}

	/**
	 * Sets position of the parameter in the function/method.
	 *
	 * @param integer $position
	 * @return \ApiGen\ReflectionParameterUnlimited
	 */
	public function setPosition($position)
	{
		$this->position = (int) $position;
		return $this;
	}

	/**
	 * Sets declaring function.
	 *
	 * @param \ApiGen\ReflectionFunctionBase $declaringFunction
	 * @return \ApiGen\ReflectionParameterUnlimited
	 */
	public function setDeclaringFunction(ReflectionFunctionBase $declaringFunction)
	{
		$this->declaringFunction = $declaringFunction;
		return $this;
	}

	/**
	 * Returns the name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Returns if the reflection object is internal.
	 *
	 * @return boolean
	 */
	public function isInternal()
	{
		return false;
	}

	/**
	 * Returns if the reflection object is user defined.
	 *
	 * @return boolean
	 */
	public function isUserDefined()
	{
		return true;
	}

	/**
	 * Returns if the current reflection comes from a tokenized source.
	 *
	 * @return boolean
	 */
	public function isTokenized()
	{
		return true;
	}

	/**
	 * Returns an element pretty (docblock compatible) name.
	 *
	 * @return string
	 */
	public function getPrettyName()
	{
		return str_replace('()', '($' . $this->name . ')', $this->declaringFunction->getPrettyName());
	}

	/**
	 * Returns the declaring class.
	 *
	 * @return \Apigen\ReflectionClass|null
	 */
	public function getDeclaringClass()
	{
		return $this->declaringFunction->getDeclaringClass();
	}

	/**
	 * Returns the declaring class name.
	 *
	 * @return string|null
	 */
	public function getDeclaringClassName()
	{
		return $this->declaringFunction->getDeclaringClassName();
	}

	/**
	 * Returns the declaring function.
	 *
	 * @return \ApiGen\ReflectionFunctionBase
	 */
	public function getDeclaringFunction()
	{
		return $this->declaringFunction;
	}

	/**
	 * Returns the declaring function name.
	 *
	 * @return string
	 */
	public function getDeclaringFunctionName()
	{
		return $this->declaringFunction->getName();
	}

	/**
	 * Returns the definition start line number in the file.
	 *
	 * @return integer
	 */
	public function getStartLine()
	{
		return $this->declaringFunction->getStartLine();
	}

	/**
	 * Returns the definition end line number in the file.
	 *
	 * @return integer
	 */
	public function getEndLine()
	{
		return $this->declaringFunction->getEndLine();
	}

	/**
	 * Returns the appropriate docblock definition.
	 *
	 * @return string|boolean
	 */
	public function getDocComment()
	{
		return false;
	}

	/**
	 * Returns the default value.
	 *
	 * @return mixed
	 */
	public function getDefaultValue()
	{
		return null;
	}

	/**
	 * Returns the part of the source code defining the paramter default value.
	 *
	 * @return string
	 */
	public function getDefaultValueDefinition()
	{
		return '';
	}

	/**
	 * Returns if a default value for the parameter is available.
	 *
	 * @return boolean
	 */
	public function isDefaultValueAvailable()
	{
		return false;
	}

	/**
	 * Returns the position within all parameters.
	 *
	 * @return integer
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * Returns if the parameter expects an array.
	 *
	 * @return boolean
	 */
	public function isArray()
	{
		return TokenReflection\ReflectionParameter::ARRAY_TYPE_HINT === $this->typeHint;
	}

	public function isCallable()
	{
		return TokenReflection\ReflectionParameter::CALLABLE_TYPE_HINT === $this->typeHint;
	}

	/**
	 * Returns reflection of the required class of the value.
	 *
	 * @return \ApiGen\ReflectionClass|null
	 */
	public function getClass()
	{
		$className = $this->getClassName();
		return null === $className ? null : self::$parsedClasses[$className];
	}

	/**
	 * Returns the required class name of the value.
	 *
	 * @return string|null
	 */
	public function getClassName()
	{
		if ($this->isArray() || $this->isCallable()) {
			return null;
		}

		if (isset(self::$parsedClasses[$this->typeHint])) {
			return $typeHint;
		}

		return null;
	}

	/**
	 * Returns if the the parameter allows NULL.
	 *
	 * @return boolean
	 */
	public function allowsNull()
	{
		return true;
	}

	/**
	 * Returns if the parameter is optional.
	 *
	 * @return boolean
	 */
	public function isOptional()
	{
		return true;
	}

	/**
	 * Returns if the parameter value is passed by reference.
	 *
	 * @return boolean
	 */
	public function isPassedByReference()
	{
		return false;
	}

	/**
	 * Returns if the paramter value can be passed by value.
	 *
	 * @return boolean
	 */
	public function canBePassedByValue()
	{
		return false;
	}

	/**
	 * If the parameter can be used unlimited times.
	 *
	 * @return boolean
	 */
	public function isUnlimited()
	{
		return true;
	}

	/**
	 * Retrieves a property or method value.
	 *
	 * @param string $name Property name
	 * @return mixed
	 */
	public function __get($name)
	{
		$key = ucfirst($name);
		if (isset(self::$reflectionMethods[$this->reflectionType]['get' . $key])) {
			return $this->{'get' . $key}();
		}

		if (isset(self::$reflectionMethods[$this->reflectionType]['is' . $key])) {
			return $this->{'is' . $key}();
		}

		return null;
	}

	/**
	 * Checks if the given property exists.
	 *
	 * @param mixed $name Property name
	 * @return boolean
	 */
	public function __isset($name)
	{
		$key = ucfirst($name);
		return isset(self::$reflectionMethods[$this->reflectionType]['get' . $key]) || isset(self::$reflectionMethods[$this->reflectionType]['is' . $key]);
	}
}
