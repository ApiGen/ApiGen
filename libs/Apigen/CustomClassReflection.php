<?php

/**
 * API Generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 *
 * This source file is subject to the "Nette license", and/or
 * GPL license. For more information please see http://nette.org
 */

namespace Apigen;

use NetteX;



/**
 * Smarter class/interface reflection.
 * @author     David Grudl
 */
class CustomClassReflection extends NetteX\Reflection\ClassReflection
{
	private $package = NULL;


	/**
	 * Returns namespace or package name.
	 * @return string
	 */
	public function getNamespaceName()
	{
		if ($this->package === NULL) {
			if ($this->inNamespace() || $this->isInternal()) {
				$this->package = parent::getNamespaceName();

			} elseif ($this->hasAnnotation('package')) {
				$this->package = $this->getAnnotation('package'); // found in class-level DocBlock

			} elseif (preg_match('#\*\s+@package\s+(\S+)#', file_get_contents($this->getFileName()), $matches)) {
				$this->package = $matches[1]; // found in page-level DocBlock
			}
		}
		return $this->package;
	}



	/**
	 * Returns interfaces declared by inspected class.
	 * @return array of CustomClassReflection
	 */
	public function getOwnInterfaces()
	{
		$parent = $this->getParentClass();
		return array_filter($this->getInterfaces(), function($interface) use ($parent) {
			return !$parent || !$parent->implementsInterface($interface->getName());
		});
	}



	/**
	 * Returns visible methods declared by inspected class.
	 * @return array of NetteX\Reflection\MethodReflection
	 */
	public function getOwnMethods()
	{
		$me = $this->getName();
		return array_filter($this->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED), function($method) use ($me) {
			return $method->declaringClass->name === $me && !$method->hasAnnotation('internal');
		});
	}



	/**
	 * Returns visible properties declared by inspected class.
	 * @return array of NetteX\Reflection\PropertyReflection
	 */
	public function getOwnProperties()
	{
		$me = $this->getName();
		return array_filter($this->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED), function($property) use ($me) {
			return $property->declaringClass->name === $me && !$property->hasAnnotation('internal');
		});
	}



	/**
	 * Returns constants declared by inspected class.
	 * @return array of string
	 */
	public function getOwnConstants()
	{
		return array_diff_assoc($this->getConstants(), $this->getParentClass() ? $this->getParentClass()->getConstants() : array());
	}

}
