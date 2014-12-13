<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

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
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $typeHint;

	/**
	 * @var string
	 */
	protected $shortDescription;

	/**
	 * @var string
	 */
	protected $longDescription;

	/**
	 * @var integer
	 */
	protected $startLine;

	/**
	 * @var integer
	 */
	protected $endLine;

	/**
	 * @var bool
	 */
	protected $readOnly;

	/**
	 * @var bool
	 */
	protected $writeOnly;

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
	 * @return \TokenReflection\Broker
	 */
	public function getBroker()
	{
		return $this->declaringClass->getBroker();
	}


	/**
	 * @return integer
	 */
	public function getStartPosition()
	{
		return $this->declaringClass->getStartPosition();
	}


	/**
	 * @return integer
	 */
	public function getEndPosition()
	{
		return $this->declaringClass->getEndPosition();
	}


	/**
	 * @return bool
	 */
	public function isMagic()
	{
		return TRUE;
	}


	/**
	 * @return ReflectionExtension|NULL
	 */
	public function getExtension()
	{
		return NULL;
	}


	/**
	 * @return bool
	 */
	public function getExtensionName()
	{
		return FALSE;
	}


	/**
	 * @return bool
	 */
	public function isDocumented()
	{
		if ($this->isDocumented === NULL) {
			$this->isDocumented = self::$config->deprecated || ! $this->isDeprecated();
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
	 * @return string
	 */
	public function getDeclaringClassName()
	{
		return $this->declaringClass->getName();
	}


	/**
	 * @return mixed
	 */
	public function getDefaultValue()
	{
		return NULL;
	}


	/**
	 * @return string
	 */
	public function getDefaultValueDefinition()
	{
		return '';
	}


	/**
	 * @return bool
	 */
	public function isDefault()
	{
		return FALSE;
	}


	/**
	 * @return integer
	 */
	public function getModifiers()
	{
		return InternalReflectionProperty::IS_PUBLIC;
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
	public function isInternal()
	{
		return FALSE;
	}


	/**
	 * @return ReflectionClass|NULL
	 */
	public function getDeclaringTrait()
	{
		return $this->declaringClass->isTrait() ? $this->declaringClass : NULL;
	}


	/**
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
	 * @return string
	 */
	public function getFileName()
	{
		return $this->declaringClass->getFileName();
	}


	/**
	 * @return bool
	 */
	public function isUserDefined()
	{
		return TRUE;
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

		if ($annotations = $this->getAnnotation('var')) {
			$docComment .= sprintf("@var %s\n", $annotations[0]);
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
