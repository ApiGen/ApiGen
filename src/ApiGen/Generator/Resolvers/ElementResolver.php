<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator\Resolvers;

use ApiGen\Parser\ParserResult;
use ApiGen\Reflection\ReflectionBase;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\ReflectionParameter;
use ApiGen\Reflection\ReflectionProperty;
use TokenReflection\Resolver;


class ElementResolver
{

	/**
	 * @var array
	 */
	private $simpleTypes = [
		'boolean' => 1,
		'integer' => 1,
		'float' => 1,
		'string' => 1,
		'array' => 1,
		'object' => 1,
		'resource' => 1,
		'callback' => 1,
		'callable' => 1,
		'NULL' => 1,
		'false' => 1,
		'true' => 1,
		'mixed' => 1
	];

	/**
	 * @var ParserResult
	 */
	private $parserResult;


	public function __construct(ParserResult $parserResult)
	{
		$this->parserResult = $parserResult;
	}


	/**
	 * Tries to resolve string as class, interface or exception name.
	 *
	 * @param string $className
	 * @param string $namespace
	 * @return ReflectionClass
	 */
	public function getClass($className, $namespace = '')
	{
		$parsedClasses = $this->parserResult->getClasses();
		if (isset($parsedClasses[$namespace . '\\' . $className])) {
			$class = $parsedClasses[$namespace . '\\' . $className];

		} elseif (isset($parsedClasses[ltrim($className, '\\')])) {
			$class = $parsedClasses[ltrim($className, '\\')];

		} else {
			return NULL;
		}

		/** @var ReflectionClass $class */
		if ( ! $class->isDocumented()) {
			return NULL;
		}

		return $class;
	}


	/**
	 * @param string $constantName
	 * @param string $namespace
	 * @return ReflectionConstant
	 */
	public function getConstant($constantName, $namespace = '')
	{
		$parsedConstants = $this->parserResult->getConstants();
		if (isset($parsedConstants[$namespace . '\\' . $constantName])) {
			$constant = $parsedConstants[$namespace . '\\' . $constantName];

		} elseif (isset($parsedConstants[ltrim($constantName, '\\')])) {
			$constant = $parsedConstants[ltrim($constantName, '\\')];

		} else {
			return NULL;
		}

		/** @var ReflectionConstant $constant */
		if ( ! $constant->isDocumented()) {
			return NULL;
		}

		return $constant;
	}


	/**
	 * @param string $functionName
	 * @param string $namespace
	 * @return ReflectionFunction
	 */
	public function getFunction($functionName, $namespace = '')
	{
		$parsedFunctions = $this->parserResult->getFunctions();
		if (isset($parsedFunctions[$namespace . '\\' . $functionName])) {
			$function = $parsedFunctions[$namespace . '\\' . $functionName];

		} elseif (isset($parsedFunctions[ltrim($functionName, '\\')])) {
			$function = $parsedFunctions[ltrim($functionName, '\\')];

		} else {
			return NULL;
		}

		/** @var ReflectionFunction $function */
		if ( ! $function->isDocumented()) {
			return NULL;
		}

		return $function;
	}


	/**
	 * Tries to parse a definition of a class/method/property/constant/function
	 *
	 * @param string $definition
	 * @param ReflectionElement|ReflectionParameter $context Link context
	 * @param string $expectedName
	 * @return ReflectionElement|NULL
	 */
	public function resolveElement($definition, $context, &$expectedName = NULL)
	{
		if ($this->isSimpleType($definition)) {
			return NULL;
		}

		$originalContext = $context;
		$context = $this->correctContextForParameterOrClassMember($context);
		if ($context === NULL) {
			return NULL;
		}

		// self, $this references
		if ($definition === 'self' || $definition === '$this') {
			return $context instanceof ReflectionClass ? $context : NULL;
		}

		$definitionBase = substr($definition, 0, strcspn($definition, '\\:'));
		$namespaceAliases = $context->getNamespaceAliases();
		$className = Resolver::resolveClassFqn($definition, $namespaceAliases, $context->getNamespaceName());

		if ($resolved = $this->resolveIfParsed($definition, $context)) {
			return $resolved;
		}

		if ( ! empty($definitionBase) && isset($namespaceAliases[$definitionBase]) && $definition !== $className) {
			// Aliased class
			$expectedName = $className;

			if (strpos($className, ':') === FALSE) {
				return $this->getClass($className, $context->getNamespaceName());

			} else {
				$definition = $className;
			}
		}

		if (($pos = strpos($definition, '::')) || ($pos = strpos($definition, '->'))) {
			$context = $this->resolveContextForClassProperty($definition, $context, $pos);
			$definition = substr($definition, $pos + 2);

		} elseif ($originalContext instanceof ReflectionParameter) {
			return NULL;
		}

		if ( ! $this->isContextUsable($context)) {
			return NULL;
		}

		return $this->resolveIfInContext($definition, $context);
	}


	/**
	 * @param ReflectionClass|ReflectionFunction|ReflectionElement $context
	 * @return ReflectionClass|ReflectionFunction
	 */
	private function correctContextForParameterOrClassMember($context)
	{
		if ($context instanceof ReflectionParameter && $context->getDeclaringClassName() === NULL) {
			// Parameter of function in namespace or global space
			return $this->getFunction($context->getDeclaringFunctionName());

		} elseif ($context instanceof ReflectionMethod || $context instanceof ReflectionParameter
			|| ($context instanceof ReflectionConstant && $context->getDeclaringClassName() !== NULL)
			|| $context instanceof ReflectionProperty
		) {
			// Member of a class
			return $this->getClass($context->getDeclaringClassName());
		}
		return $context;
	}


	/**
	 * @param string $definition
	 * @param int $pos
	 * @param ReflectionElement $context
	 * @return ReflectionClass
	 */
	private function resolveContextForSelfProperty($definition, $pos, $context)
	{
		$class = $this->getClass(substr($definition, 0, $pos), $context->getNamespaceName());
		if ($class === NULL) {
			$class = $this->getClass(Resolver::resolveClassFqn(
				substr($definition, 0, $pos), $context->getNamespaceAliases(), $context->getNamespaceName()
			));
		}
		return $class;
	}


	/**
	 * @param string $definition
	 * @return bool
	 */
	private function isSimpleType($definition)
	{
		if (empty($definition) || isset($this->simpleTypes[$definition])) {
			return TRUE;
		}

		return FALSE;
	}


	/**
	 * @param string $definition
	 * @param ReflectionFunction $context
	 * @return ReflectionClass|ReflectionConstant|ReflectionFunction|NULL
	 */
	private function resolveIfParsed($definition, $context)
	{
		$definition = $this->removeEndBrackets($definition);
		if ($class = $this->getClass($definition, $context->getNamespaceName())) {
			return $class;

		} elseif ($constant = $this->getConstant($definition, $context->getNamespaceName())) {
			return $constant;

		} elseif ($function = $this->getFunction($definition, $context->getNamespaceName())) {
			return $function;
		}
		return NULL;
	}


	/**
	 * @param $definition
	 * @param ReflectionClass $context
	 * @return ReflectionConstant|ReflectionMethod|ReflectionProperty|NULL
	 */
	private function resolveIfInContext($definition, ReflectionClass $context)
	{
		$definition = $this->removeEndBrackets($definition);
		$definition = $this->removeStartDollar($definition);

		if ($context->hasProperty($definition)) {
			return $context->getProperty($definition);

		} elseif ($context->hasMethod($definition)) {
			return $context->getMethod($definition);

		} elseif ($context->hasConstant($definition)) {
			return $context->getConstant($definition);
		}
		return NULL;
	}


	/**
	 * @param string $definition
	 * @return string
	 */
	private function removeEndBrackets($definition)
	{
		if (substr($definition, -2) === '()') {
			return substr($definition, 0, -2);
		}
		return $definition;
	}


	/**
	 * @param string $definition
	 * @return string
	 */
	private function removeStartDollar($definition)
	{
		if ($definition{0} === '$') {
			return substr($definition, 1);
		}
		return $definition;
	}


	/**
	 * @param string $definition
	 * @param ReflectionClass $context
	 * @param int $pos
	 * @return ReflectionClass
	 */
	private function resolveContextForClassProperty($definition, ReflectionClass $context, $pos)
	{
		// Class::something or Class->something
		if (strpos($definition, 'parent::') === 0 && ($parentClassName = $context->getParentClassName())) {
			return $this->getClass($parentClassName);

		} elseif (strpos($definition, 'self::') !== 0) {
			return $this->resolveContextForSelfProperty($definition, $pos, $context);
		}
		return $context;
	}


	/**
	 * @param mixed $context
	 * @return bool
	 */
	private function isContextUsable($context)
	{
		if ($context === NULL || $context instanceof ReflectionConstant || $context instanceof ReflectionFunction) {
			return FALSE;
		}
		return TRUE;
	}

}
