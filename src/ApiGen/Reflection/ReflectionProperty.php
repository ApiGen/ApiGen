<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use TokenReflection\IReflectionProperty;


/**
 * Property reflection envelope
 */
class ReflectionProperty extends ReflectionElement implements IReflectionProperty
{

	/**
	 * @return bool
	 */
	public function isReadOnly()
	{
		return FALSE;
	}


	/**
	 * @return bool
	 */
	public function isWriteOnly()
	{
		return FALSE;
	}


	/**
	 * @return bool
	 */
	public function isMagic()
	{
		return FALSE;
	}


	/**
	 * @return string
	 */
	public function getTypeHint()
	{
		if ($annotations = $this->getAnnotation('var')) {
			list($types) = preg_split('~\s+|$~', $annotations[0], 2);
			if ( ! empty($types) && '$' !== $types[0]) {
				return $types;
			}
		}

		try {
			$type = gettype($this->getDefaultValue());
			if (strtolower($type) !== 'null') {
				return $type;
			}

		} catch (\Exception $e) {
			return 'mixed';
		}

		return 'mixed';
	}


	/**
	 * @return ReflectionClass|null
	 */
	public function getDeclaringClass()
	{
		$className = $this->reflection->getDeclaringClassName();
		$parsedClasses = $this->getParsedClasses();
		return $className === NULL ? NULL : $parsedClasses[$className];
	}


	/**
	 * @return string
	 */
	public function getDeclaringClassName()
	{
		return $this->reflection->getDeclaringClassName();
	}


	/**
	 * @return mixed
	 */
	public function getDefaultValue()
	{
		return $this->reflection->getDefaultValue();
	}


	/**
	 * Returns the part of the source code defining the property default value.
	 *
	 * @return string
	 */
	public function getDefaultValueDefinition()
	{
		return $this->reflection->getDefaultValueDefinition();
	}


	/**
	 * Returns if the property was created at compile time.
	 *
	 * @return bool
	 */
	public function isDefault()
	{
		return $this->reflection->isDefault();
	}


	/**
	 * @return integer
	 */
	public function getModifiers()
	{
		return $this->reflection->getModifiers();
	}


	/**
	 * @return bool
	 */
	public function isPrivate()
	{
		return $this->reflection->isPrivate();
	}


	/**
	 * @return bool
	 */
	public function isProtected()
	{
		return $this->reflection->isProtected();
	}


	/**
	 * @return bool
	 */
	public function isPublic()
	{
		return $this->reflection->isPublic();
	}


	/**
	 * @return bool
	 */
	public function isStatic()
	{
		return $this->reflection->isStatic();
	}


	/**
	 * @return ReflectionClass|null
	 */
	public function getDeclaringTrait()
	{
		$traitName = $this->reflection->getDeclaringTraitName();
		$parsedClasses = $this->getParsedClasses();
		return $traitName === NULL ? NULL : $parsedClasses[$traitName];
	}


	/**
	 * @return string|null
	 */
	public function getDeclaringTraitName()
	{
		return $this->reflection->getDeclaringTraitName();
	}


	/**
	 * @return bool
	 */
	public function isValid()
	{
		if ($class = $this->getDeclaringClass()) {
			return $class->isValid();
		}

		return TRUE;
	}


	public function getValue($object)
	{
		throw new \Exception('Not implemented nor required');
	}


	public function setAccessible($accessible)
	{
		throw new \Exception('Not implemented nor required');
	}


	public function isAccessible()
	{
		throw new \Exception('Not implemented nor required');
	}


	public function setValue($object, $value)
	{
		throw new \Exception('Not implemented nor required');
	}


	public function __toString()
	{
		throw new \Exception('Not implemented nor required');
		return '';
	}

}
