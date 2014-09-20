<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use ApiGen\Generator\Generator;
use ReflectionProperty as InternalReflectionProperty;
use TokenReflection\IReflection;


/**
 * Envelope for magic properties that are defined
 * only as @property, @property-read or @property-write annotation.
 *
 * @method ReflectionPropertyMagic  setName(string $name)
 * @method ReflectionPropertyMagic  setTypeHint()
 * @method ReflectionPropertyMagic  setShortDescription()
 * @method ReflectionPropertyMagic  setStartLine()
 * @method ReflectionPropertyMagic  setEndLine()
 * @method ReflectionPropertyMagic  setReadOnly()
 * @method ReflectionPropertyMagic  setWriteOnly()
 * @method ReflectionPropertyMagic  setDeclaringClass(ReflectionClass $declaringClass)
 * @method string                   getTypeHint()
 * @method bool                     isReadOnly()
 * @method bool                     isWriteOnly()
 * @method ReflectionClass          getDeclaringClass()
 */
class ReflectionPropertyMagic extends ReflectionProperty
{

	/**
	 * Property name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Defines a type hint of parameter values.
	 *
	 * @var string
	 */
	protected $typeHint;

	/**
	 * Short description.
	 *
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
	 * If the property is read-only.
	 *
	 * @var boolean
	 */
	protected $readOnly;

	/**
	 * If the property is write-only.
	 *
	 * @var boolean
	 */
	protected $writeOnly;

	/**
	 * The declaring class.
	 *
	 * @var ReflectionClass
	 */
	protected $declaringClass;


	/**
	 * Constructor.
	 *
	 * @param \TokenReflection\IReflection $reflection Inspected reflection
	 * @param \ApiGen\Generator $generator ApiGen generator
	 */
	public function __construct(IReflection $reflection = NULL, Generator $generator = NULL)
	{
		$this->reflectionType = get_class($this);
		if ( ! isset(self::$reflectionMethods[$this->reflectionType])) {
			self::$reflectionMethods[$this->reflectionType] = array_flip(get_class_methods($this));
		}
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
	 * Overrides parent method.
	 *
	 * @return int
	 */
	public function getStartLine()
	{
		return $this->startLine;
	}


	/**
	 * Overrides parent method.
	 *
	 * @return string
	 */
	public function getShortDescription()
	{
		return $this->shortDescription;
	}


	/**
	 * Overrides parent method.
	 *
	 * @return string
	 */
	public function getLongDescription()
	{
		return $this->longDescription;
	}


	/**
	 * Overrides parent method.
	 *
	 * @return int
	 */
	public function getEndLine()
	{
		return $this->endLine;
	}


	/**
	 * Returns the reflection broker used by this reflection object.
	 *
	 * @return \TokenReflection\Broker
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
	 * Returns if the property is magic.
	 *
	 * @return boolean
	 */
	public function isMagic()
	{
		return TRUE;
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
	 * Returns if the property should be documented.
	 *
	 * @return boolean
	 */
	public function isDocumented()
	{
		if ($this->isDocumented === NULL) {
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
		if ($this->annotations === NULL) {
			$this->annotations = array();
		}
		return $this->annotations;
	}


	/**
	 * Returns the name of the declaring class.
	 *
	 * @return string
	 */
	public function getDeclaringClassName()
	{
		return $this->declaringClass->getName();
	}


	/**
	 * Returns the property default value.
	 *
	 * @return mixed
	 */
	public function getDefaultValue()
	{
		return NULL;
	}


	/**
	 * Returns the part of the source code defining the property default value.
	 *
	 * @return string
	 */
	public function getDefaultValueDefinition()
	{
		return '';
	}


	/**
	 * Returns if the property was created at compile time.
	 *
	 * @return boolean
	 */
	public function isDefault()
	{
		return FALSE;
	}


	/**
	 * Returns property modifiers.
	 *
	 * @return integer
	 */
	public function getModifiers()
	{
		return InternalReflectionProperty::IS_PUBLIC;
	}


	/**
	 * Returns if the property is private.
	 *
	 * @return boolean
	 */
	public function isPrivate()
	{
		return FALSE;
	}


	/**
	 * Returns if the property is protected.
	 *
	 * @return boolean
	 */
	public function isProtected()
	{
		return FALSE;
	}


	/**
	 * Returns if the property is public.
	 *
	 * @return boolean
	 */
	public function isPublic()
	{
		return TRUE;
	}


	/**
	 * Returns if the property is static.
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
	 * Returns the property declaring trait.
	 *
	 * @return ReflectionClass|NULL
	 */
	public function getDeclaringTrait()
	{
		return $this->declaringClass->isTrait() ? $this->declaringClass : NULL;
	}


	/**
	 * Returns the declaring trait name.
	 *
	 * @return string|NULL
	 */
	public function getDeclaringTraitName()
	{
		if ($declaringTrait = $this->getDeclaringTrait()) {
			return $declaringTrait->getName();
		}
		return NULL;
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
		return sprintf('%s::$%s', $this->declaringClass->getName(), $this->name);
	}


	/**
	 * Returns the file name the property is defined in.
	 *
	 * @return string
	 */
	public function getFileName()
	{
		return $this->declaringClass->getFileName();
	}


	/**
	 * Returns if the property is user defined.
	 *
	 * @return boolean
	 */
	public function isUserDefined()
	{
		return TRUE;
	}


	/**
	 * Returns if the property comes from a tokenized source.
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

		if ($annotations = $this->getAnnotation('var')) {
			$docComment .= sprintf("@var %s\n", $annotations[0]);
		}

		$docComment .= "*/\n";

		return $docComment;
	}


	/**
	 * Checks if there is a particular annotation.
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function hasAnnotation($name)
	{
		$annotations = $this->getAnnotations();
		return array_key_exists($name, $annotations);
	}


	/**
	 * Returns a particular annotation value.
	 *
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
