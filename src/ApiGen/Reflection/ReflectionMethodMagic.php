<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Reflection\Parts\StartLineEndLine;
use ApiGen\Reflection\Parts\StartPositionEndPositionMagic;
use TokenReflection\IReflection;


/**
 * @method  ReflectionMethodMagic   setName(string $name)
 * @method  ReflectionMethodMagic   setShortDescription(string $shortDescription)
 * @method  ReflectionMethodMagic   setReturnsReference(bool $returnsReference)
 * @method  ReflectionMethodMagic   setParameters(array $parameters)
 * @method  ReflectionMethodMagic   setDeclaringClass(ReflectionClass $declaringClass)
 */
class ReflectionMethodMagic extends ReflectionMethod
{

	use StartLineEndLine;
	use StartPositionEndPositionMagic;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $shortDescription;

	/**
	 * @var bool
	 */
	protected $returnsReference;

	/**
	 * @var ReflectionClass
	 */
	protected $declaringClass;


	public function __construct(IReflection $reflection = NULL)
	{
		$this->reflectionType = get_class($this);
		if ( ! isset(self::$reflectionMethods[$this->reflectionType])) {
			self::$reflectionMethods[$this->reflectionType] = array_flip(get_class_methods($this));
		}
	}


	/**
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
	public function getShortDescription()
	{
		return $this->shortDescription;
	}


	/**
	 * @return string
	 */
	public function getLongDescription()
	{
		return $this->shortDescription;
	}


	/**
	 * @return bool
	 */
	public function returnsReference()
	{
		return $this->returnsReference;
	}


	/**
	 * @return bool
	 */
	public function isMagic()
	{
		return TRUE;
	}


	/**
	 * Returns the unqualified name (UQN).
	 *
	 * @return string
	 */
	public function getShortName()
	{
		return $this->name;
	}


	/**
	 * @return bool
	 */
	public function isDocumented()
	{
		if ($this->isDocumented === NULL) {
			$deprecated = $this->configuration->getOption(CO::DEPRECATED);
			$this->isDocumented = $deprecated || ! $this->isDeprecated();
		}

		return $this->isDocumented;
	}


	/**
	 * @return bool
	 */
	public function isDeprecated()
	{
		return $this->declaringClass->isDeprecated();
	}


	/**
	 * @return string
	 */
	public function getPackageName()
	{
		return $this->declaringClass->getPackageName();
	}


	/**
	 * @return string
	 */
	public function getNamespaceName()
	{
		return $this->declaringClass->getNamespaceName();
	}


	/**
	 * @return array
	 */
	public function getAnnotations()
	{
		if ($this->annotations === NULL) {
			$this->annotations = [];
		}
		return $this->annotations;
	}


	/**
	 * @return ReflectionClass|null
	 */
	public function getDeclaringClass()
	{
		return $this->declaringClass;
	}


	/**
	 * @return string|null
	 */
	public function getDeclaringClassName()
	{
		return $this->declaringClass->getName();
	}


	/**
	 * @return bool
	 */
	public function isAbstract()
	{
		return FALSE;
	}


	/**
	 * @return bool
	 */
	public function isFinal()
	{
		return FALSE;
	}


	/**
	 * @return bool
	 */
	public function isPrivate()
	{
		return FALSE;
	}


	/**
	 * @return bool
	 */
	public function isProtected()
	{
		return FALSE;
	}


	/**
	 * @return bool
	 */
	public function isPublic()
	{
		return TRUE;
	}


	/**
	 * @return bool
	 */
	public function isStatic()
	{
		return FALSE;
	}


	/**
	 * @return bool
	 */
	public function isConstructor()
	{
		return FALSE;
	}


	/**
	 * @return bool
	 */
	public function isDestructor()
	{
		return FALSE;
	}


	/**
	 * @return ReflectionClass|null
	 */
	public function getDeclaringTrait()
	{
		return $this->declaringClass->isTrait() ? $this->declaringClass : NULL;
	}


	/**
	 * @return string|null
	 */
	public function getDeclaringTraitName()
	{
		if ($declaringTrait = $this->getDeclaringTrait()) {
			return $declaringTrait->getName();
		}
		return NULL;
	}


	/**
	 * @return ReflectionMethod|null
	 */
	public function getImplementedMethod()
	{
		return NULL;
	}


	/**
	 * @return ReflectionMethod|null
	 */
	public function getOverriddenMethod()
	{
		$parent = $this->declaringClass->getParentClass();
		if ($parent === NULL) {
			return NULL;
		}

		foreach ($parent->getMagicMethods() as $method) {
			if ($method->getName() === $this->name) {
				return $method;
			}
		}

		return NULL;
	}


	/**
	 * @return string|NULL
	 */
	public function getOriginalName()
	{
		return $this->getName();
	}


	/**
	 * @return ReflectionMethod|NULL
	 */
	public function getOriginal()
	{
		return NULL;
	}


	/**
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}


	/**
	 * @return integer
	 */
	public function getNumberOfParameters()
	{
		return count($this->parameters);
	}


	/**
	 * @return integer
	 */
	public function getNumberOfRequiredParameters()
	{
		$count = 0;
		array_walk($this->parameters, function (ReflectionParameter $parameter) use (&$count) {
			if ( ! $parameter->isOptional()) {
				$count++;
			}
		});
		return $count;
	}


	/**
	 * Returns imported namespaces and aliases from the declaring namespace.
	 *
	 * @return array
	 */
	public function getNamespaceAliases()
	{
		return $this->declaringClass->getNamespaceAliases();
	}


	/**
	 * Returns an property pretty (docblock compatible) name.
	 *
	 * @return string
	 */
	public function getPrettyName()
	{
		return sprintf('%s::%s()', $this->declaringClass->getName(), $this->name);
	}


	/**
	 * @return string
	 */
	public function getFileName()
	{
		return $this->declaringClass->getFileName();
	}


	/**
	 * @return bool
	 */
	public function isTokenized()
	{
		return TRUE;
	}


	/**
	 * @return string|boolean
	 */
	public function getDocComment()
	{
		$docComment = "/**\n";

		if ( ! empty($this->shortDescription)) {
			$docComment .= $this->shortDescription . "\n\n";
		}

		if ($annotations = $this->getAnnotation('param')) {
			foreach ($annotations as $annotation) {
				$docComment .= sprintf("@param %s\n", $annotation);
			}
		}

		if ($annotations = $this->getAnnotation('return')) {
			foreach ($annotations as $annotation) {
				$docComment .= sprintf("@return %s\n", $annotation);
			}
		}

		$docComment .= "*/\n";

		return $docComment;
	}


	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasAnnotation($name)
	{
		$annotations = $this->getAnnotations();
		return array_key_exists($name, $annotations);
	}


	/**
	 * @param string $name
	 * @return string|array|NULL
	 */
	public function getAnnotation($name)
	{
		$annotations = $this->getAnnotations();
		if (array_key_exists($name, $annotations)) {
			return $annotations[$name];
		}
		return NULL;
	}

}
