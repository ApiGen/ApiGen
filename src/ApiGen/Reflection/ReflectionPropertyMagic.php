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
 * Envelope for magic properties that are defined
 * only as @property, @property-read or @property-write annotation.
 */
class ReflectionPropertyMagic extends ReflectionProperty
{

	use StartLineEndLine;
	use StartPositionEndPositionMagic;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $typeHint;

	/**
	 * @var string
	 */
	private $shortDescription;

	/**
	 * @var string
	 */
	private $longDescription;

	/**
	 * @var bool
	 */
	private $readOnly;

	/**
	 * @var bool
	 */
	private $writeOnly;

	/**
	 * @var ReflectionClass
	 */
	private $declaringClass;


	public function __construct(IReflection $reflection = NULL)
	{
		$this->reflectionType = get_class($this);
		if ( ! isset(self::$reflectionMethods[$this->reflectionType])) {
			self::$reflectionMethods[$this->reflectionType] = array_flip(get_class_methods($this));
		}
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * @param string $name
	 * @return $this
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}


	/**
	 * @return mixed
	 */
	public function getTypeHint()
	{
		return $this->typeHint;
	}


	/**
	 * @param mixed $typeHint
	 * @return $this
	 */
	public function setTypeHint($typeHint)
	{
		$this->typeHint = $typeHint;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function getWriteOnly()
	{
		return $this->writeOnly;
	}


	/**
	 * @param bool $writeOnly
	 * @return $this
	 */
	public function setWriteOnly($writeOnly)
	{
		$this->writeOnly = $writeOnly;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getShortDescription()
	{
		return $this->shortDescription;
	}


	/**
	 * @param string $shortDescription
	 * @return $this
	 */
	public function setShortDescription($shortDescription)
	{
		$this->shortDescription = $shortDescription;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getLongDescription()
	{
		return $this->longDescription;
	}


	/**
	 * @return bool
	 */
	public function isReadOnly()
	{
		return $this->readOnly;
	}


	/**
	 * @param bool $readOnly
	 * @return $this
	 */
	public function setReadOnly($readOnly)
	{
		$this->readOnly = $readOnly;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function isMagic()
	{
		return TRUE;
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
	 * @return string
	 */
	public function getDeclaringClassName()
	{
		return $this->declaringClass->getName();
	}


	/**
	 * @return ReflectionClass
	 */
	public function getDeclaringClass()
	{
		return $this->declaringClass;
	}


	/**
	 * @return $this
	 */
	public function setDeclaringClass(ReflectionClass $declaringClass)
	{
		$this->declaringClass = $declaringClass;
		return $this;
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
