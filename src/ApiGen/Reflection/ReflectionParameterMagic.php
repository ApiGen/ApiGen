<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use TokenReflection;
use TokenReflection\IReflection;


/**
 * Envelope for parameters that are defined only in @param or @method annotation.
 *
 * @method ReflectionParameterMagic setName()
 * @method ReflectionParameterMagic setTypeHint()
 * @method ReflectionParameterMagic setPosition()
 * @method ReflectionParameterMagic setDefaultValueDefinition()
 * @method ReflectionParameterMagic setUnlimited()
 * @method ReflectionParameterMagic setPassedByReference()
 * @method ReflectionParameterMagic setDeclaringFunction(object)
 */
class ReflectionParameterMagic extends ReflectionParameter
{

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	private $typeHint;

	/**
	 * @var integer
	 */
	private $position;

	/**
	 * The part of the source code defining the parameter default value.
	 *
	 * @var bool
	 */
	private $defaultValueDefinition;

	/**
	 * @var bool
	 */
	private $unlimited;

	/**
	 * @var bool
	 */
	private $passedByReference;

	/**
	 * @var TokenReflection\IReflectionMethod
	 */
	private $declaringFunction;


	public function __construct(IReflection $reflection = NULL)
	{
		$this->reflectionType = get_class($this);
		if ( ! isset(self::$reflectionMethods[$this->reflectionType])) {
			self::$reflectionMethods[$this->reflectionType] = array_flip(get_class_methods($this));
		}
	}


	/**
	 * @return TokenReflection\Broker
	 */
	public function getBroker()
	{
		return $this->declaringFunction->getBroker();
	}


	/**
	 * Returns the name.
	 * Overrides parent method.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * @return string
	 */
	public function getTypeHint()
	{
		return $this->typeHint;
	}


	/**
	 * @return string
	 */
	public function getFileName()
	{
		return $this->declaringFunction->getFileName();
	}


	/**
	 * @return bool
	 */
	public function isInternal()
	{
		return FALSE;
	}


	/**
	 * @return bool
	 */
	public function isUserDefined()
	{
		return TRUE;
	}


	/**
	 * @return bool
	 */
	public function isTokenized()
	{
		return TRUE;
	}


	/**
	 * Returns an element docblock compatible name.
	 *
	 * @return string
	 */
	public function getPrettyName()
	{
		return str_replace('()', '($' . $this->name . ')', $this->declaringFunction->getPrettyName());
	}


	/**
	 * @return ReflectionClass|NULL
	 */
	public function getDeclaringClass()
	{
		return $this->declaringFunction->getDeclaringClass();
	}


	/**
	 * @return string|null
	 */
	public function getDeclaringClassName()
	{
		return $this->declaringFunction->getDeclaringClassName();
	}


	/**
	 * @return ReflectionFunctionBase
	 */
	public function getDeclaringFunction()
	{
		return $this->declaringFunction;
	}


	/**
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
	 * @return string|bool
	 */
	public function getDocComment()
	{
		return FALSE;
	}


	/**
	 * Returns the part of the source code defining the parameter default value.
	 *
	 * @return string
	 */
	public function getDefaultValueDefinition()
	{
		return $this->defaultValueDefinition;
	}


	/**
	 * @return bool
	 */
	public function isDefaultValueAvailable()
	{
		return (bool) $this->defaultValueDefinition;
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
	 * @return bool
	 */
	public function isArray()
	{
		return TokenReflection\ReflectionParameter::ARRAY_TYPE_HINT === $this->typeHint;
	}


	/**
	 * @return bool
	 */
	public function isCallable()
	{
		return TokenReflection\ReflectionParameter::CALLABLE_TYPE_HINT === $this->typeHint;
	}


	/**
	 * Returns reflection of the required class of the value.
	 *
	 * @return ReflectionClass|NULL
	 */
	public function getClass()
	{
		$className = $this->getClassName();
		$parsedClasses = $this->getParsedClasses();
		return $className === NULL ? NULL : $parsedClasses[$className];
	}


	/**
	 * Returns the required class name of the value.
	 *
	 * @return string|NULL
	 */
	public function getClassName()
	{
		if ($this->isArray() || $this->isCallable()) {
			return NULL;
		}

		$parsedClasses = $this->getParsedClasses();
		if (isset($parsedClasses[$this->typeHint])) {
			return $this->typeHint;
		}

		return NULL;
	}


	/**
	 * @return bool
	 */
	public function allowsNull()
	{
		if ($this->isArray() || $this->isCallable()) {
			return strtolower($this->defaultValueDefinition) === 'null';
		}

		return ! empty($this->defaultValueDefinition);
	}


	/**
	 * @return bool
	 */
	public function isOptional()
	{
		return $this->isDefaultValueAvailable();
	}


	/**
	 * @return bool
	 */
	public function isPassedByReference()
	{
		return $this->passedByReference;
	}


	/**
	 * @return bool
	 */
	public function canBePassedByValue()
	{
		return FALSE;
	}


	/**
	 * @return bool
	 */
	public function isUnlimited()
	{
		return $this->unlimited;
	}

}
