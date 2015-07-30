<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;

class ReflectionParameter extends ReflectionBase implements ParameterReflectionInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTypeHint()
    {
        if ($this->isArray()) {
            return 'array';

        } elseif ($this->isCallable()) {
            return 'callable';

        } elseif ($className = $this->getClassName()) {
            return $className;

        } elseif ($annotations = $this->getDeclaringFunction()->getAnnotation('param')) {
            if (! empty($annotations[$this->getPosition()])) {
                list($types) = preg_split('~\s+|$~', $annotations[$this->getPosition()], 2);
                if (! empty($types) && $types[0] !== '$') {
                    return $types;
                }
            }
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        $annotations = $this->getDeclaringFunction()->getAnnotation('param');
        if (empty($annotations[$this->getPosition()])) {
            return '';
        }

        $description = trim(strpbrk($annotations[$this->getPosition()], "\n\r\t "));
        return preg_replace('~^(\\$' . $this->getName() . '(?:,\\.{3})?)(\\s+|$)~i', '\\2', $description, 1);
    }


    /**
     * {@inheritdoc}
     */
    public function getDefaultValueDefinition()
    {
        return $this->reflection->getDefaultValueDefinition();
    }


    /**
     * {@inheritdoc}
     */
    public function isDefaultValueAvailable()
    {
        return $this->reflection->isDefaultValueAvailable();
    }


    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->reflection->getPosition();
    }


    /**
     * {@inheritdoc}
     */
    public function isArray()
    {
        return $this->reflection->isArray();
    }


    /**
     * {@inheritdoc}
     */
    public function isCallable()
    {
        return $this->reflection->isCallable();
    }


    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        $className = $this->reflection->getClassName();
        return $className === null ? null : $this->getParsedClasses()[$className];
    }


    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return $this->reflection->getClassName();
    }


    /**
     * {@inheritdoc}
     */
    public function allowsNull()
    {
        return $this->reflection->allowsNull();
    }


    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return $this->reflection->isOptional();
    }


    /**
     * {@inheritdoc}
     */
    public function isPassedByReference()
    {
        return $this->reflection->isPassedByReference();
    }


    /**
     * {@inheritdoc}
     */
    public function canBePassedByValue()
    {
        return $this->reflection->canBePassedByValue();
    }


    /**
     * {@inheritdoc}
     */
    public function getDeclaringFunction()
    {
        $functionName = $this->reflection->getDeclaringFunctionName();

        if ($className = $this->reflection->getDeclaringClassName()) {
            return $this->getParsedClasses()[$className]->getMethod($functionName);

        } else {
            return $this->parserResult->getFunctions()[$functionName];
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getDeclaringFunctionName()
    {
        return $this->reflection->getDeclaringFunctionName();
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
    public function isUnlimited()
    {
        return false;
    }
}
