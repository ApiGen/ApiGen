<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Bridge\TokenReflectionBridge;

use ApiGen\Configuration\Configuration;
use ApiGen\Parser\ParserStorage;
use ApiGen\Reflection\ReflectionBase;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\ReflectionMethodMagic;
use ApiGen\Reflection\ReflectionParameter;
use ApiGen\Reflection\ReflectionParameterMagic;
use ApiGen\Reflection\ReflectionProperty;
use Nette;
use TokenReflection\IReflectionClass;
use TokenReflection\IReflectionConstant;
use TokenReflection\IReflectionFunction;
use TokenReflection\IReflectionMethod;
use TokenReflection\IReflectionParameter;
use TokenReflection\IReflectionProperty;


class ApiGenReflectionFactory extends Nette\Object
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var ParserStorage
	 */
	private $parserStorage;


	public function __construct(Configuration $configuration, ParserStorage $parserStorage)
	{
		$this->configuration = $configuration;
		$this->parserStorage = $parserStorage;
	}


	/**
	 * @param IReflectionClass|IReflectionConstant|IReflectionFunction $tokenReflection
	 * @return ReflectionClass|ReflectionConstant|ReflectionMethod
	 */
	public function createFromReflection($tokenReflection)
	{
		$apiGenReflection = $this->createInstanceByReflectionType($tokenReflection);
		$apiGenReflection = $this->setDependencies($apiGenReflection);
		return $apiGenReflection;
	}


	/**
	 * @return ReflectionBase|ReflectionMethodMagic
	 */
	public function createMethodMagic()
	{
		$reflection = new ReflectionMethodMagic;
		$reflection = $this->setDependencies($reflection);
		return $reflection;
	}


	/**
	 * @return ReflectionBase|ReflectionParameterMagic
	 */
	public function createParameterMagic()
	{
		$reflection = new ReflectionParameterMagic;
		$reflection = $this->setDependencies($reflection);
		return $reflection;
	}


	/**
	 * @param IReflectionClass|IReflectionConstant|IReflectionMethod $reflection
	 * @return ReflectionClass|ReflectionConstant|ReflectionMethod
	 */
	private function createInstanceByReflectionType($reflection)
	{
		if ($reflection instanceof IReflectionClass) {
			return new ReflectionClass($reflection);

		} elseif ($reflection instanceof IReflectionConstant) {
			return new ReflectionConstant($reflection);

		} elseif ($reflection instanceof IReflectionMethod) {
			return new ReflectionMethod($reflection);

		} elseif ($reflection instanceof IReflectionProperty) {
			return new ReflectionProperty($reflection);

		} elseif ($reflection instanceof IReflectionParameter) {
			return new ReflectionParameter($reflection);
		}

		throw new \RuntimeException('Invalid reflection class type ' . get_class($reflection));
	}


	/**
	 * @param ReflectionBase $reflection
	 * @return ReflectionBase
	 */
	private function setDependencies($reflection)
	{
		$reflection->setConfiguration($this->configuration);
		$reflection->setParserStorage($this->parserStorage);
		$reflection->setApiGenReflectionFactory($this);
		return $reflection;
	}

}
