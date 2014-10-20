<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use ApiGen\Configuration\Configuration;
use Nette;
use TokenReflection\IReflection;


/**
 * Alters TokenReflection\IReflection functionality for ApiGen.
 */
abstract class ReflectionBase extends Nette\Object
{

	/**
	 * @var \ArrayObject
	 */
	protected static $parsedClasses;

	/**
	 * @var \ArrayObject
	 */
	protected static $parsedConstants;

	/**
	 * @var \ArrayObject
	 */
	protected static $parsedFunctions;

	/**
	 * @var Configuration
	 */
	protected static $config = NULL;

	/**
	 * Class methods cache.
	 *
	 * @var array
	 */
	protected static $reflectionMethods = array();

	/**
	 * Reflection type (reflection class).
	 *
	 * @var string
	 */
	protected $reflectionType;

	/**
	 * Inspected class reflection.
	 *
	 * @var \TokenReflection\IReflectionClass
	 */
	protected $reflection;


	/**
	 * Sets the inspected reflection.
	 */
	public function __construct(IReflection $reflection)
	{
		self::$config = Configuration::$config;
		$this->reflectionType = get_class($this);
		if ( ! isset(self::$reflectionMethods[$this->reflectionType])) {
			self::$reflectionMethods[$this->reflectionType] = array_flip(get_class_methods($this));
		}

		$this->reflection = $reflection;
	}


	/**
	 * Returns the reflection broker used by this reflection object.
	 *
	 * @return \TokenReflection\Broker
	 */
	public function getBroker()
	{
		return $this->reflection->getBroker();
	}


	/**
	 * Returns the name (FQN).
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->reflection->getName();
	}


	/**
	 * Returns an element pretty (docblock compatible) name.
	 *
	 * @return string
	 */
	public function getPrettyName()
	{
		return $this->reflection->getPrettyName();
	}


	/**
	 * Returns if the reflection object is internal.
	 *
	 * @return boolean
	 */
	public function isInternal()
	{
		return $this->reflection->isInternal();
	}


	/**
	 * Returns if the reflection object is user defined.
	 *
	 * @return boolean
	 */
	public function isUserDefined()
	{
		return $this->reflection->isUserDefined();
	}


	/**
	 * Returns if the current reflection comes from a tokenized source.
	 *
	 * @return boolean
	 */
	public function isTokenized()
	{
		return $this->reflection->isTokenized();
	}


	/**
	 * Returns the file name the reflection object is defined in.
	 *
	 * @return string
	 */
	public function getFileName()
	{
		return $this->reflection->getFileName();
	}


	/**
	 * Returns the definition start line number in the file.
	 *
	 * @return integer
	 */
	public function getStartLine()
	{
		$startLine = $this->reflection->getStartLine();

		if ($doc = $this->getDocComment()) {
			$startLine -= substr_count($doc, "\n") + 1;
		}

		return $startLine;
	}


	/**
	 * Returns the definition end line number in the file.
	 *
	 * @return integer
	 */
	public function getEndLine()
	{
		return $this->reflection->getEndLine();
	}

}
