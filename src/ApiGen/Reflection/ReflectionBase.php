<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use ApiGen\Bridge\TokenReflectionBridge\ApiGenReflectionFactory;
use ApiGen\Configuration\Configuration;
use ApiGen\Elements\Elements;
use ApiGen\Parser\ParserStorage;
use ArrayObject;
use Nette;
use TokenReflection\Broker;
use TokenReflection\IReflection;
use TokenReflection\IReflectionClass;
use TokenReflection\IReflectionExtension;
use TokenReflection\IReflectionFunction;
use TokenReflection\IReflectionMethod;
use TokenReflection\IReflectionParameter;


/**
 * @method setConfiguration(object)
 * @method setParserStorage(object)
 * @method setApiGenReflectionFactory(object)
 */
abstract class ReflectionBase extends Nette\Object
{

	/**
	 * @var Configuration
	 */
	protected $configuration;

	/**
	 * @var ParserStorage
	 */
	protected $parserStorage;

	/**
	 * @var ApiGenReflectionFactory
	 */
	protected $apiGenReflectionFactory;

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
	 * @var IReflection|IReflectionClass|IReflectionMethod|IReflectionFunction|IReflectionExtension|IReflectionParameter
	 */
	protected $reflection;


	public function __construct(IReflection $reflection)
	{
		$this->reflectionType = get_class($this);
		if ( ! isset( self::$reflectionMethods[$this->reflectionType])) {
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
	 * @return bool
	 */
	public function isInternal()
	{
		return $this->reflection->isInternal();
	}


	/**
	 * @return bool
	 */
	public function isUserDefined()
	{
		return $this->reflection->isUserDefined();
	}


	/**
	 * @return bool
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
	 * @return int
	 */
	public function getStartLine()
	{
		$startLine = $this->reflection->getStartLine();
		if ($doc = $this->reflection->getDocComment()) {
			$startLine -= substr_count($doc, "\n") + 1;
		}

		return $startLine;
	}


	/**
	 * @return int
	 */
	public function getEndLine()
	{
		return $this->reflection->getEndLine();
	}


	/**
	 * @return ArrayObject
	 */
	protected function getParsedClasses()
	{
		return $this->parserStorage->getElementsByType(Elements::CLASSES);
	}

}
