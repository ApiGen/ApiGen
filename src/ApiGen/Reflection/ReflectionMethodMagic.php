<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use ApiGen\Generator\Generator;
use ReflectionProperty as InternalReflectionMethod;
use TokenReflection\Broker;
use TokenReflection\IReflection;


/**
 * Envelope for magic methods that are defined only as @method annotation.
 */
class ReflectionMethodMagic extends ReflectionMethod
{

	/**
	 * Method name.
	 *
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
	 * @var boolean
	 */
	protected $returnsReference;

	/**
	 * @var ReflectionClass
	 */
	protected $declaringClass;


	public function __construct(IReflection $reflection = NULL, Generator $generator = NULL)
	{
		$this->reflectionType = get_class($this);
		if ( ! isset(self::$reflectionMethods[$this->reflectionType])) {
			self::$reflectionMethods[$this->reflectionType] = array_flip(get_class_methods($this));
		}
	}


	/**
	 * @param string $name
	 * @return ReflectionMethodMagic
	 */
	public function setName($name)
	{
		$this->name = (string) $name;
		return $this;

	}


	/**
	 * @param string $shortDescription
	 * @return ReflectionMethodMagic
	 */
	public function setShortDescription($shortDescription)
	{
		$this->shortDescription = (string) $shortDescription;
		return $this;
	}


	/**
	 * @param integer $startLine
	 * @return ReflectionMethodMagic
	 */
	public function setStartLine($startLine)
	{
		$this->startLine = (int) $startLine;
		return $this;
	}


	/**
	 * @param integer $endLine
	 * @return ReflectionMethodMagic
	 */
	public function setEndLine($endLine)
	{
		$this->endLine = (int) $endLine;
		return $this;
	}


	/**
	 * @param boolean $returnsReference
	 * @return ReflectionMethodMagic
	 */
	public function setReturnsReference($returnsReference)
	{
		$this->returnsReference = (bool) $returnsReference;
		return $this;
	}


	/**
	 * @param array $parameters
	 * @return ReflectionMethodMagic
	 */
	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters;
		return $this;
	}


	/**
	 * @param ReflectionClass $declaringClass
	 * @return ReflectionMethodMagic
	 */
	public function setDeclaringClass(ReflectionClass $declaringClass)
	{
		$this->declaringClass = $declaringClass;
		return $this;
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
	 * Returns the long description.
	 *
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
	 * @return boolean
	 */
	public function returnsReference()
	{
		return $this->returnsReference;
	}


	/**
	 * Returns if the property is magic.
	 *
	 * @return boolean
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
	 * @return boolean
	 */
	public function getExtensionName()
	{
		return FALSE;
	}


	/**
	 * Returns if the method should be documented.
	 *
	 * @return boolean
	 */
	public function isDocumented()
	{
		if (NULL === $this->isDocumented) {
			$this->isDocumented = self::$config->deprecated || ! $this->isDeprecated();
		}

		return $this->isDocumented;
	}


	/**
	 * Returns if the property is deprecated.
	 *
	 * @return boolean
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
	 * Returns property namespace name.
	 *
	 * @return string
	 */
	public function getNamespaceName()
	{
		return $this->declaringClass->getNamespaceName();
	}


	/**
	 * Returns property annotations.
	 *
	 * @return array
	 */
	public function getAnnotations()
	{
		if (NULL === $this->annotations) {
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
	 * @return boolean
	 */
	public function isAbstract()
	{
		return FALSE;
	}


	/**
	 * Returns if the method is final.
	 *
	 * @return boolean
	 */
	public function isFinal()
	{
		return FALSE;
	}


	/**
	 * Returns if the method is private.
	 *
	 * @return boolean
	 */
	public function isPrivate()
	{
		return FALSE;
	}


	/**
	 * Returns if the method is protected.
	 *
	 * @return boolean
	 */
	public function isProtected()
	{
		return FALSE;
	}


	/**
	 * Returns if the method is public.
	 *
	 * @return boolean
	 */
	public function isPublic()
	{
		return TRUE;
	}


	/**
	 * Returns if the method is static.
	 *
	 * @return boolean
	 */
	public function isStatic()
	{
		return FALSE;
	}


	/**
	 * Returns if the property is internal.
	 *
	 * @return boolean
	 */
	public function isInternal()
	{
		return FALSE;
	}


	/**
	 * Returns if the method is a constructor.
	 *
	 * @return boolean
	 */
	public function isConstructor()
	{
		return FALSE;
	}


	/**
	 * Returns if the method is a destructor.
	 *
	 * @return boolean
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
	 * @return boolean
	 */
	public function isUserDefined()
	{
		return TRUE;
	}


	/**
	 * Returns if the method comes from a tokenized source.
	 *
	 * @return boolean
	 */
	public function isTokenized()
	{
		return TRUE;
	}


	/**
	 * Returns the appropriate docblock definition.
	 *
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
	 * @return boolean
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


	/**
	 * Retrieves a property or method value.
	 *
	 * @param string $name
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

		return NULL;
	}


	/**
	 * @param mixed $name
	 * @return boolean
	 */
	public function __isset($name)
	{
		$key = ucfirst($name);
		return isset(self::$reflectionMethods[$this->reflectionType]['get' . $key]) || isset(self::$reflectionMethods[$this->reflectionType]['is' . $key]);
	}

}
