<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use ApiGen\FileSystem\FileSystem;


/**
 * Constant reflection envelope.
 * Alters TokenReflection\IReflectionConstant functionality for ApiGen.
 */
class ReflectionConstant extends ReflectionElement
{

	/**
	 * Returns the name (FQN).
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->reflection->getName();
	}


	/**
	 * Returns the unqualified name (UQN).
	 *
	 * @return string
	 */
	public function getShortName()
	{
		return $this->reflection->getShortName();
	}


	/**
	 * Returns constant type hint.
	 *
	 * @return string
	 */
	public function getTypeHint()
	{
		if ($annotations = $this->getAnnotation('var')) {
			list($types) = preg_split('~\s+|$~', $annotations[0], 2);
			if ( ! empty($types)) {
				return $types;
			}
		}

		try {
			$type = gettype($this->getValue());
			if (strtolower($type) !== 'null') {
				return $type;
			}

		} catch (\Exception $e) {
			return NULL;
		}
	}


	/**
	 * Returns the constant declaring class.
	 *
	 * @return ReflectionClass|NULL
	 */
	public function getDeclaringClass()
	{
		$className = $this->reflection->getDeclaringClassName();
		return $className === NULL ? NULL : self::$parsedClasses[$className];
	}


	/**
	 * Returns the name of the declaring class.
	 *
	 * @return string|NULL
	 */
	public function getDeclaringClassName()
	{
		return $this->reflection->getDeclaringClassName();
	}


	/**
	 * Returns the constant value.
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->reflection->getValue();
	}


	/**
	 * Returns the constant value definition.
	 *
	 * @return string
	 */
	public function getValueDefinition()
	{
		return $this->reflection->getValueDefinition();
	}


	/**
	 * Returns if the constant is valid.
	 *
	 * @return bool
	 */
	public function isValid()
	{
		if ($this->reflection instanceof \TokenReflection\Invalid\ReflectionConstant) {
			return FALSE;
		}

		if ($class = $this->getDeclaringClass()) {
			return $class->isValid();
		}

		return TRUE;
	}


	/**
	 * Returns if the constant should be documented.
	 *
	 * @return bool
	 */
	public function isDocumented()
	{
		if ($this->isDocumented === NULL && parent::isDocumented() && $this->reflection->getDeclaringClassName() === NULL) {
			$fileName = FileSystem::unPharPath($this->reflection->getFilename());
			foreach (self::$config->skipDocPath as $mask) {
				if (fnmatch($mask, $fileName, FNM_NOESCAPE)) {
					$this->isDocumented = FALSE;
					break;
				}
			}
		}

		if ($this->isDocumented === TRUE) {
			foreach (self::$config->skipDocPrefix as $prefix) {
				if (strpos($this->reflection->getName(), $prefix) === 0) {
					$this->isDocumented = FALSE;
					break;
				}
			}
		}

		return $this->isDocumented;
	}

}
