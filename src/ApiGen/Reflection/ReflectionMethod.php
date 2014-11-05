<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use TokenReflection\IReflectionMethod;


/**
 * Method reflection envelope
 */
class ReflectionMethod extends ReflectionFunctionBase implements IReflectionMethod
{

	/**
	 * Returns if the method is magic.
	 *
	 * @return bool
	 */
	public function isMagic()
	{
		return FALSE;
	}


	/**
	 * Returns the method declaring class.
	 *
	 * @return ReflectionClass|NULL
	 */
	public function getDeclaringClass()
	{
		$className = $this->reflection->getDeclaringClassName();
		$parsedClasses = $this->getParsedClasses();
		return $className === NULL ? NULL : $parsedClasses[$className];
	}


	/**
	 * Returns the declaring class name.
	 *
	 * @return string|NULL
	 */
	public function getDeclaringClassName()
	{
		return $this->reflection->getDeclaringClassName();
	}


	/**
	 * Returns method modifiers.
	 *
	 * @return integer
	 */
	public function getModifiers()
	{
		return $this->reflection->getModifiers();
	}


	/**
	 * Returns if the method is abstract.
	 *
	 * @return bool
	 */
	public function isAbstract()
	{
		return $this->reflection->isAbstract();
	}


	/**
	 * Returns if the method is final.
	 *
	 * @return bool
	 */
	public function isFinal()
	{
		return $this->reflection->isFinal();
	}


	/**
	 * Returns if the method is private.
	 *
	 * @return bool
	 */
	public function isPrivate()
	{
		return $this->reflection->isPrivate();
	}


	/**
	 * Returns if the method is protected.
	 *
	 * @return bool
	 */
	public function isProtected()
	{
		return $this->reflection->isProtected();
	}


	/**
	 * Returns if the method is public.
	 *
	 * @return bool
	 */
	public function isPublic()
	{
		return $this->reflection->isPublic();
	}


	/**
	 * Returns if the method is static.
	 *
	 * @return bool
	 */
	public function isStatic()
	{
		return $this->reflection->isStatic();
	}


	/**
	 * Returns if the method is a constructor.
	 *
	 * @return bool
	 */
	public function isConstructor()
	{
		return $this->reflection->isConstructor();
	}


	/**
	 * Returns if the method is a destructor.
	 *
	 * @return bool
	 */
	public function isDestructor()
	{
		return $this->reflection->isDestructor();
	}


	/**
	 * Returns the method declaring trait.
	 *
	 * @return ReflectionClass|NULL
	 */
	public function getDeclaringTrait()
	{
		$traitName = $this->reflection->getDeclaringTraitName();
		$parsedClasses = $this->getParsedClasses();
		return $traitName === NULL ? NULL : $parsedClasses[$traitName];
	}


	/**
	 * Returns the declaring trait name.
	 *
	 * @return string|NULL
	 */
	public function getDeclaringTraitName()
	{
		return $this->reflection->getDeclaringTraitName();
	}


	/**
	 * Returns the overridden method.
	 *
	 * @return ReflectionMethod|NULL
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
	 * Returns the overridden method.
	 *
	 * @return ReflectionMethod|NULL
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
	 * Returns the original name when importing from a trait.
	 *
	 * @return string|null
	 */
	public function getOriginalName()
	{
		return $this->reflection->getOriginalName();
	}


	/**
	 * Returns the original modifiers value when importing from a trait.
	 *
	 * @return integer|null
	 */
	public function getOriginalModifiers()
	{
		return $this->reflection->getOriginalModifiers();
	}


	/**
	 * Returns the original method when importing from a trait.
	 *
	 * @return ReflectionMethod|NULL
	 */
	public function getOriginal()
	{
		$originalName = $this->reflection->getOriginalName();
		if ($originalName === NULL) {
			return NULL;
		}
		$originalDeclaringClassName = $this->reflection->getOriginal()->getDeclaringClassName();
		$parsedClasses = $this->getParsedClasses();
		return $parsedClasses[$originalDeclaringClassName]->getMethod($originalName);
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


	public function isClosure()
	{
		throw new \Exception('Not implemented nor required');
	}


	public function getStaticVariables()
	{
		throw new \Exception('Not implemented nor required');
	}


	public function is($filter = NULL)
	{
		throw new \Exception('Not implemented nor required');
	}


	public function getPrototype()
	{
		throw new \Exception('Not implemented nor required');
	}


	public function invoke($object, $args)
	{
		throw new \Exception('Not implemented nor required');
	}


	public function invokeArgs($object, array $args)
	{
		throw new \Exception('Not implemented nor required');
	}


	public function setAccessible($accessible)
	{
		throw new \Exception('Not implemented nor required');
	}


	public function getClosure($object)
	{
		throw new \Exception('Not implemented nor required');
	}

}
