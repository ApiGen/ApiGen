<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;


class ReflectionProperty extends ReflectionElement
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
			if ( ! empty($types) && $types[0] !== '$') {
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
	 * @return int
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
	 * @return ReflectionClass|NULL
	 */
	public function getDeclaringTrait()
	{
		$traitName = $this->reflection->getDeclaringTraitName();
		$parsedClasses = $this->getParsedClasses();
		return $traitName === NULL ? NULL : $parsedClasses[$traitName];
	}


	/**
	 * @return string|NULL
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

}
