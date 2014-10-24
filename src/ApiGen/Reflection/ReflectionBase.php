<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use ApiGen\Configuration\Configuration;
use ArrayObject;
use Nette;
use Nette\Utils\ArrayHash;
use TokenReflection\Broker;
use TokenReflection\IReflection;
use TokenReflection\IReflectionClass;


/**
 * Alters TokenReflection\IReflection functionality for ApiGen.
 */
abstract class ReflectionBase extends Nette\Object implements IReflection
{

	/**
	 * @var ArrayObject
	 */
	protected static $parsedClasses;

	/**
	 * @var ArrayObject
	 */
	protected static $parsedConstants;

	/**
	 * @var ArrayObject
	 */
	protected static $parsedFunctions;

	/**
	 * @var ArrayHash
	 */
	protected static $config;

	/**
	 * Class methods cache.
	 *
	 * @var array
	 */
	protected static $reflectionMethods = array();

	/**
	 * @var string
	 */
	protected $reflectionType;

	/**
	 * @var IReflectionClass
	 */
	protected $reflection;


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
	 * @return Broker
	 */
	public function getBroker()
	{
		return $this->reflection->getBroker();
	}


	/**
	 * Returns FQN name.
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
	 * @return boolean
	 */
	public function isInternal()
	{
		return $this->reflection->isInternal();
	}


	/**
	 * @return boolean
	 */
	public function isUserDefined()
	{
		return $this->reflection->isUserDefined();
	}


	/**
	 * @return boolean
	 */
	public function isTokenized()
	{
		return $this->reflection->isTokenized();
	}


	/**
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
