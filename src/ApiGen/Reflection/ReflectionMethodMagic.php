<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use ReflectionProperty as InternalReflectionMethod;
use TokenReflection\Broker;
use TokenReflection\IReflection;


/**
 * Envelope for magic methods that are defined only as @method annotation.
 *
 * @method  ReflectionMethodMagic   setName()
 * @method  ReflectionMethodMagic   setShortDescription()
 * @method  ReflectionMethodMagic   setStartLine()
 * @method  ReflectionMethodMagic   setEndLine()
 * @method  ReflectionMethodMagic   setReturnsReference()
 * @method  ReflectionMethodMagic   setParameters()
 * @method  ReflectionMethodMagic   setDeclaringClass(object)
 */
class ReflectionMethodMagic extends ReflectionMethod
{

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $shortDescription;

	/**
	 * Start line number in the file.
	 *
	 * @var integer
	 */
	protected $startLine;

	/**
	 * End line number in the file.
	 *
	 * @var integer
	 */
	protected $endLine;

	/**
	 * If the method returns reference.
	 *
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
		$this->setReflectionMethodsByType($this->reflectionType, $this);
	}


	/**
	 * Returns the reflection broker used by this reflection object.
	 *
	 * @return Broker
	 */
	public function getBroker()
	{
		return $this->declaringClass->getBroker();
	}


	/**
	 * Returns the start position in the file token stream.
	 *
	 * @return integer
	 */
	public function getStartPosition()
	{
		return $this->declaringClass->getStartPosition();
	}


	/**
	 * Returns the end position in the file token stream.
	 *
	 * @return integer
	 */
	public function getEndPosition()
	{
		return $this->declaringClass->getEndPosition();
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
	 * Returns the definition start line number in the file.
	 *
	 * @return integer
	 */
	public function getStartLine()
	{
		return $this->startLine;
	}


	/**
	 * Returns the definition end line number in the file.
	 *
	 * @return integer
	 */
	public function getEndLine()
	{
		return $this->endLine;
	}


	/**
	 * Returns if the function/method returns its value as reference.
	 *
	 * @return bool
	 */
	public function returnsReference()
	{
		return $this->returnsReference;
	}


	/**
	 * Returns if the property is magic.
	 *
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
	 * Returns the PHP extension reflection.
	 *
	 * @return ReflectionExtension|NULL
	 */
	public function getExtension()
	{
		return NULL;
	}


	/**
	 * Returns the PHP extension name.
	 *
	 * @return bool
	 */
	public function getExtensionName()
	{
		return FALSE;
	}


	/**
	 * Returns if the method should be documented.
	 *
	 * @return bool
	 */
	public function isDocumented()
	{
		if ($this->isDocumented === NULL) {
			$deprecated = $this->configuration->getOption('deprecated');
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
	 * Returns property package name (including subpackage name).
	 *
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
			$this->annotations = array();
		}
		return $this->annotations;
	}


	/**
	 * Returns the method declaring class.
	 *
	 * @return \ApiGen\ReflectionClass|null
	 */
	public function getDeclaringClass()
	{
		return $this->declaringClass;
	}


	/**
	 * Returns the declaring class name.
	 *
	 * @return string|null
	 */
	public function getDeclaringClassName()
	{
		return $this->declaringClass->getName();
	}


	/**
	 * Returns method modifiers.
	 *
	 * @return integer
	 */
	public function getModifiers()
	{
		return InternalReflectionMethod::IS_PUBLIC;
	}


	/**
	 * Returns if the method is abstract.
	 *
	 * @return bool
	 */
	public function isAbstract()
	{
		return FALSE;
	}


	/**
	 * Returns if the method is final.
	 *
	 * @return bool
	 */
	public function isFinal()
	{
		return FALSE;
	}


	/**
	 * Returns if the method is private.
	 *
	 * @return bool
	 */
	public function isPrivate()
	{
		return FALSE;
	}


	/**
	 * Returns if the method is protected.
	 *
	 * @return bool
	 */
	public function isProtected()
	{
		return FALSE;
	}


	/**
	 * Returns if the method is public.
	 *
	 * @return bool
	 */
	public function isPublic()
	{
		return TRUE;
	}


	/**
	 * Returns if the method is static.
	 *
	 * @return bool
	 */
	public function isStatic()
	{
		return FALSE;
	}


	/**
	 * Returns if the property is internal.
	 *
	 * @return bool
	 */
	public function isInternal()
	{
		return FALSE;
	}


	/**
	 * Returns if the method is a constructor.
	 *
	 * @return bool
	 */
	public function isConstructor()
	{
		return FALSE;
	}


	/**
	 * Returns if the method is a destructor.
	 *
	 * @return bool
	 */
	public function isDestructor()
	{
		return FALSE;
	}


	/**
	 * Returns the method declaring trait.
	 *
	 * @return \ApiGen\ReflectionClass|null
	 */
	public function getDeclaringTrait()
	{
		return $this->declaringClass->isTrait() ? $this->declaringClass : NULL;
	}


	/**
	 * Returns the declaring trait name.
	 *
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
	 * Returns the overridden method.
	 *
	 * @return \ApiGen\ReflectionMethod|null
	 */
	public function getImplementedMethod()
	{
		return NULL;
	}


	/**
	 * Returns the overridden method.
	 *
	 * @return \ApiGen\ReflectionMethod|null
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
	 * Returns the original name when importing from a trait.
	 *
	 * @return string|NULL
	 */
	public function getOriginalName()
	{
		return $this->getName();
	}


	/**
	 * Returns the original modifiers value when importing from a trait.
	 *
	 * @return integer|NULL
	 */
	public function getOriginalModifiers()
	{
		return $this->getModifiers();
	}


	/**
	 * Returns the original method when importing from a trait.
	 *
	 * @return ReflectionMethod|NULL
	 */
	public function getOriginal()
	{
		return NULL;
	}


	/**
	 * Returns a list of method parameters.
	 *
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}


	/**
	 * Returns the number of parameters.
	 *
	 * @return integer
	 */
	public function getNumberOfParameters()
	{
		return count($this->parameters);
	}


	/**
	 * Returns the number of required parameters.
	 *
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
	 * Returns the file name the method is defined in.
	 *
	 * @return string
	 */
	public function getFileName()
	{
		return $this->declaringClass->getFileName();
	}


	/**
	 * Returns if the method is user defined.
	 *
	 * @return bool
	 */
	public function isUserDefined()
	{
		return TRUE;
	}


	/**
	 * Returns if the method comes from a tokenized source.
	 *
	 * @return bool
	 */
	public function isTokenized()
	{
		return TRUE;
	}


	/**
	 * Returns the appropriate docblock definition.
	 *
	 * @return string|bool
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
