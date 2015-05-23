<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Parser\Reflection\Parts\Visibility;


class ReflectionMethod extends ReflectionFunctionBase implements MethodReflectionInterface
{

	use Visibility;


	/**
	 * {@inheritdoc}
	 */
	public function isMagic()
	{
		return FALSE;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getDeclaringClass()
	{
		$className = $this->reflection->getDeclaringClassName();
		return $className === NULL ? NULL : $this->getParsedClasses()[$className];
	}


	/**
	 * {@inheritdoc}
	 */
	public function getDeclaringClassName()
	{
		return $this->reflection->getDeclaringClassName();
	}


	/**
	 * {@inheritdoc}
	 */
	public function isAbstract()
	{
		return $this->reflection->isAbstract();
	}


	/**
	 * {@inheritdoc}
	 */
	public function isFinal()
	{
		return $this->reflection->isFinal();
	}


	/**
	 * {@inheritdoc}
	 */
	public function isStatic()
	{
		return $this->reflection->isStatic();
	}


	/**
	 * {@inheritdoc}
	 */
	public function getDeclaringTrait()
	{
		$traitName = $this->reflection->getDeclaringTraitName();
		return $traitName === NULL ? NULL : $this->getParsedClasses()[$traitName];
	}


	/**
	 * {@inheritdoc}
	 */
	public function getDeclaringTraitName()
	{
		return $this->reflection->getDeclaringTraitName();
	}


	/**
	 * {@inheritdoc}
	 */
	public function getOriginalName()
	{
		return $this->reflection->getOriginalName();
	}


	/**
	 * {@inheritdoc}
	 */
	public function isValid()
	{
		if ($class = $this->getDeclaringClass()) {
			return $class->isValid();
		}

		return TRUE;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getImplementedMethod()
	{
		foreach ($this->getDeclaringClass()->getOwnInterfaces() as $interface) {
			if ($interface->hasMethod($this->getName())) {
				return $interface->getMethod($this->getName());
			}
		}
		return NULL;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getOverriddenMethod()
	{
		$parent = $this->getDeclaringClass()->getParentClass();
		if ($parent === NULL) {
			return NULL;
		}
		foreach ($parent->getMethods() as $method) {
			if ($method->getName() === $this->getName()) {
				if ( ! $method->isPrivate() && ! $method->isAbstract()) {
					return $method;

				} else {
					return NULL;
				}
			}
		}
		return NULL;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getOriginal()
	{
		$originalName = $this->reflection->getOriginalName();
		if ($originalName === NULL) {
			return NULL;
		}
		$originalDeclaringClassName = $this->reflection->getOriginal()->getDeclaringClassName();
		return $this->getParsedClasses()[$originalDeclaringClassName]->getMethod($originalName);
	}

}
