<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use TokenReflection;

class ReflectionConstant extends ReflectionElement implements ConstantReflectionInterface
{

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->reflection->getName();
    }


    /**
     * {@inheritdoc}
     */
    public function getShortName()
    {
        return $this->reflection->getShortName();
    }


    /**
     * {@inheritdoc}
     */
    public function getTypeHint()
    {
        if ($annotations = $this->getAnnotation('var')) {
            list($types) = preg_split('~\s+|$~', $annotations[0], 2);
            if (! empty($types)) {
                return $types;
            }
        }

        try {
            $type = gettype($this->getValue());
            if (strtolower($type) !== 'null') {
                return $type;
            }

        } catch (\Exception $e) {
            return null;
        }
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
    public function getValue()
    {
        return $this->reflection->getValue();
    }


    /**
     * @return string
     */
    public function getValueDefinition()
    {
        return $this->reflection->getValueDefinition();
    }


    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        if ($this->reflection instanceof TokenReflection\Invalid\ReflectionConstant) {
            return false;
        }

        if ($class = $this->getDeclaringClass()) {
            return $class->isValid();
        }

        return true;
    }
}
