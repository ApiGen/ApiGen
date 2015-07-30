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
        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function getDeclaringClass()
    {
        $className = $this->reflection->getDeclaringClassName();
        return $className === null ? null : $this->getParsedClasses()[$className];
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
        return $traitName === null ? null : $this->getParsedClasses()[$traitName];
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

        return true;
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
        return null;
    }


    /**
     * {@inheritdoc}
     */
    public function getOverriddenMethod()
    {
        $parent = $this->getDeclaringClass()->getParentClass();
        if ($parent === null) {
            return null;
        }
        foreach ($parent->getMethods() as $method) {
            if ($method->getName() === $this->getName()) {
                if (! $method->isPrivate() && ! $method->isAbstract()) {
                    return $method;

                } else {
                    return null;
                }
            }
        }
        return null;
    }


    /**
     * {@inheritdoc}
     */
    public function getOriginal()
    {
        $originalName = $this->reflection->getOriginalName();
        if ($originalName === null) {
            return null;
        }
        $originalDeclaringClassName = $this->reflection->getOriginal()->getDeclaringClassName();
        return $this->getParsedClasses()[$originalDeclaringClassName]->getMethod($originalName);
    }
}
