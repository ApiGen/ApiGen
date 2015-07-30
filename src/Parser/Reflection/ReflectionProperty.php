<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Parser\Reflection\Parts\Visibility;

class ReflectionProperty extends ReflectionElement implements PropertyReflectionInterface
{

    use Visibility;


    /**
     * {@inheritdoc}
     */
    public function isReadOnly()
    {
        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function isWriteOnly()
    {
        return false;
    }


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
    public function getTypeHint()
    {
        if ($annotations = $this->getAnnotation('var')) {
            list($types) = preg_split('~\s+|$~', $annotations[0], 2);
            if (! empty($types) && $types[0] !== '$') {
                return $types;
            }
        }

        try {
            $type = gettype($this->getDefaultValue());
            if (strtolower($type) !== 'null') {
                return $type;
            }

        } catch (\Exception $e) {
            return;
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
     * @return string
     */
    public function getDefaultValueDefinition()
    {
        return $this->reflection->getDefaultValueDefinition();
    }


    /**
     * {@inheritdoc}
     */
    public function getDefaultValue()
    {
        return $this->reflection->getDefaultValue();
    }


    /**
     * {@inheritdoc}
     */
    public function isDefault()
    {
        return $this->reflection->isDefault();
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
    public function getShortName()
    {
        return $this->getName();
    }
}
