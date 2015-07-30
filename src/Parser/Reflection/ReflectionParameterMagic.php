<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicParameterReflectionInterface;
use TokenReflection;

class ReflectionParameterMagic extends ReflectionParameter implements MagicParameterReflectionInterface
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $typeHint;

    /**
     * @var int
     */
    private $position;

    /**
     * @var bool
     */
    private $defaultValueDefinition;

    /**
     * @var bool
     */
    private $unlimited;

    /**
     * @var bool
     */
    private $passedByReference;

    /**
     * @var ReflectionMethodMagic
     */
    private $declaringFunction;


    public function __construct(array $settings)
    {
        $this->name = $settings['name'];
        $this->position = $settings['position'];
        $this->typeHint = $settings['typeHint'];
        $this->defaultValueDefinition = $settings['defaultValueDefinition'];
        $this->unlimited = $settings['unlimited'];
        $this->passedByReference = $settings['passedByReference'];
        $this->declaringFunction = $settings['declaringFunction'];

        $this->reflectionType = get_class($this);
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * {@inheritdoc}
     */
    public function getTypeHint()
    {
        return $this->typeHint;
    }


    /**
     * {@inheritdoc}
     */
    public function getFileName()
    {
        return $this->declaringFunction->getFileName();
    }


    /**
     * {@inheritdoc}
     */
    public function isTokenized()
    {
        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function getPrettyName()
    {
        return str_replace('()', '($' . $this->name . ')', $this->declaringFunction->getPrettyName());
    }


    /**
     * {@inheritdoc}
     */
    public function getDeclaringClass()
    {
        return $this->declaringFunction->getDeclaringClass();
    }


    /**
     * {@inheritdoc}
     */
    public function getDeclaringClassName()
    {
        return $this->declaringFunction->getDeclaringClassName();
    }


    /**
     * {@inheritdoc}
     */
    public function getDeclaringFunction()
    {
        return $this->declaringFunction;
    }


    /**
     * {@inheritdoc}
     */
    public function getDeclaringFunctionName()
    {
        return $this->declaringFunction->getName();
    }


    /**
     * {@inheritdoc}
     */
    public function getStartLine()
    {
        return $this->declaringFunction->getStartLine();
    }


    /**
     * {@inheritdoc}
     */
    public function getEndLine()
    {
        return $this->declaringFunction->getEndLine();
    }


    /**
     * {@inheritdoc}
     */
    public function getDocComment()
    {
        return '';
    }


    /**
     * {@inheritdoc}
     */
    public function isDefaultValueAvailable()
    {
        return (bool) $this->defaultValueDefinition;
    }


    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->position;
    }


    /**
     * {@inheritdoc}
     */
    public function isArray()
    {
        return TokenReflection\ReflectionParameter::ARRAY_TYPE_HINT === $this->typeHint;
    }


    /**
     * {@inheritdoc}
     */
    public function isCallable()
    {
        return TokenReflection\ReflectionParameter::CALLABLE_TYPE_HINT === $this->typeHint;
    }


    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        $className = $this->getClassName();
        return $className === null ? null : $this->getParsedClasses()[$className];
    }


    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        if ($this->isArray() || $this->isCallable()) {
            return null;
        }
        if (isset($this->getParsedClasses()[$this->typeHint])) {
            return $this->typeHint;
        }

        return null;
    }


    /**
     * {@inheritdoc}
     */
    public function allowsNull()
    {
        if ($this->isArray() || $this->isCallable()) {
            return strtolower($this->defaultValueDefinition) === 'null';
        }

        return ! empty($this->defaultValueDefinition);
    }


    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return $this->isDefaultValueAvailable();
    }


    /**
     * {@inheritdoc}
     */
    public function isPassedByReference()
    {
        return $this->passedByReference;
    }


    /**
     * {@inheritdoc}
     */
    public function canBePassedByValue()
    {
        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function isUnlimited()
    {
        return $this->unlimited;
    }
}
