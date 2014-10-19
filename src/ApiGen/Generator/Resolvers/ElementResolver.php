<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator\Resolvers;

use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\ReflectionParameter;
use ApiGen\Reflection\ReflectionProperty;
use ArrayObject;
use Nette;
use TokenReflection\Resolver;


/**
 * Gets variables extracted by Generator.
 *
 * @method  ElementResolver setParsedClasses(object)
 * @method  ElementResolver setParsedConstants(object)
 * @method  ElementResolver setParsedFunctions(object)
 */
class ElementResolver extends Nette\Object
{

	/**
	 * @var ArrayObject
	 */
	private $parsedClasses;

	/**
	 * @var ArrayObject
	 */
	private $parsedConstants;

	/**
	 * @var ArrayObject
	 */
	private $parsedFunctions;

	/**
	 * @var array
	 */
	private $simpleTypes = array(
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
	);


	public function __construct()
	{
		$this->parsedClasses = new ArrayObject;
		$this->parsedFunctions = new ArrayObject;
		$this->parsedConstants = new ArrayObject;
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
		if (isset($this->parsedClasses[$namespace . '\\' . $className])) {
			$class = $this->parsedClasses[$namespace . '\\' . $className];

		} elseif (isset($this->parsedClasses[ltrim($className, '\\')])) {
			$class = $this->parsedClasses[ltrim($className, '\\')];

		} else {
			return NULL;
		}

		/** @var ReflectionClass $class */
		if ( ! $class->isDocumented()) { // class is not "documented"
			return NULL;
		}

		return $class;
	}


	/**
	 * Tries to resolve type as constant name.
	 *
	 * @param string $constantName
	 * @param string $namespace
	 * @return ReflectionConstant
	 */
	public function getConstant($constantName, $namespace = '')
	{
		if (isset($this->parsedConstants[$namespace . '\\' . $constantName])) {
			$constant = $this->parsedConstants[$namespace . '\\' . $constantName];

		} elseif (isset($this->parsedConstants[ltrim($constantName, '\\')])) {
			$constant = $this->parsedConstants[ltrim($constantName, '\\')];

		} else {
			return NULL;
		}

		/** @var ReflectionConstant $constant */
		if ( ! $constant->isDocumented()) { // constant is not "documented"
			return NULL;
		}

		return $constant;
	}


	/**
	 * Tries to resolve type as function name.
	 *
	 * @param string $functionName
	 * @param string $namespace
	 * @return ReflectionFunction
	 */
	public function getFunction($functionName, $namespace = '')
	{
		if (isset($this->parsedFunctions[$namespace . '\\' . $functionName])) {
			$function = $this->parsedFunctions[$namespace . '\\' . $functionName];

		} elseif (isset($this->parsedFunctions[ltrim($functionName, '\\')])) {
			$function = $this->parsedFunctions[ltrim($functionName, '\\')];

		} else {
			return NULL;
		}

		/** @var ReflectionFunction $function */
		if ( ! $function->isDocumented()) { // function is not "documented"
			return NULL;
		}

		return $function;
	}


	/**
	 * Tries to parse a definition of a class/method/property/constant/function
	 * and returns the appropriate instance if successful.
	 *
	 * @param string $definition
	 * @param ReflectionElement|ReflectionParameter $context Link context
	 * @param string $expectedName
	 * @return ReflectionElement|NULL
	 */
	public function resolveElement($definition, $context, &$expectedName = NULL)
	{
		// No simple type resolving
		if (empty($definition) || isset($this->simpleTypes[$definition])) {
			return NULL;
		}

		$originalContext = $context;

		if ($context instanceof ReflectionParameter && NULL === $context->getDeclaringClassName()) {
			// Parameter of function in namespace or global space
			$context = $this->getFunction($context->getDeclaringFunctionName());

		} elseif ($context instanceof ReflectionMethod || $context instanceof ReflectionParameter
			|| ($context instanceof ReflectionConstant && NULL !== $context->getDeclaringClassName())
			|| $context instanceof ReflectionProperty
		) {
			// Member of a class
			$context = $this->getClass($context->getDeclaringClassName());
		}

		if ($context === NULL) {
			return NULL;
		}

		// self, $this references
		if ($definition === 'self' || $definition === '$this') {
			return $context instanceof ReflectionClass ? $context : NULL;
		}

		$definitionBase = substr($definition, 0, strcspn($definition, '\\:'));
		$namespaceAliases = $context->getNamespaceAliases();
		$className = Resolver::resolveClassFQN($definition, $namespaceAliases, $context->getNamespaceName());
		if ( ! empty($definitionBase) && isset($namespaceAliases[$definitionBase]) && $definition !== $className) {
			// Aliased class
			$expectedName = $className;

			if (strpos($className, ':') === FALSE) {
				return $this->getClass($className, $context->getNamespaceName());

			} else {
				$definition = $className;
			}

		} elseif ($class = $this->getClass($definition, $context->getNamespaceName())) {
			return $class;

		} elseif ($constant = $this->getConstant($definition, $context->getNamespaceName())) {
			return $constant;

		} elseif (($function = $this->getFunction($definition, $context->getNamespaceName()))
			|| (substr($definition, -2) === '()' && ($function = $this->getFunction(substr($definition, 0, -2), $context->getNamespaceName())))
		) {
			return $function;
		}

		if (($pos = strpos($definition, '::')) || ($pos = strpos($definition, '->'))) {
			// Class::something or Class->something
			if (strpos($definition, 'parent::') === 0 && ($parentClassName = $context->getParentClassName())) {
				$context = $this->getClass($parentClassName);

			} elseif (strpos($definition, 'self::') !== 0) {
				$class = $this->getClass(substr($definition, 0, $pos), $context->getNamespaceName());
				if ($class === NULL) {
					$class = $this->getClass(Resolver::resolveClassFQN(
						substr($definition, 0, $pos), $context->getNamespaceAliases(), $context->getNamespaceName()
					));
				}

				$context = $class;
			}

			$definition = substr($definition, $pos + 2);

		} elseif ($originalContext instanceof ReflectionParameter) {
			return NULL;
		}

		// No usable context
		if ($context === NULL || $context instanceof ReflectionConstant || $context instanceof ReflectionFunction) {
			return NULL;
		}

		if ($context->hasProperty($definition)) {
			return $context->getProperty($definition);

		} elseif ($definition{0} === '$' && $context->hasProperty(substr($definition, 1))) {
			return $context->getProperty(substr($definition, 1));

		} elseif ($context->hasMethod($definition)) {
			return $context->getMethod($definition);

		} elseif (substr($definition, -2) === '()' && $context->hasMethod(substr($definition, 0, -2))) {
			return $context->getMethod(substr($definition, 0, -2));

		} elseif ($context->hasConstant($definition)) {
			return $context->getConstant($definition);
		}

		return NULL;
	}

}
