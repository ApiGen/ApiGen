<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;
use ApiGen\Parser\Reflection\Parts\IsDocumentedMagic;
use ApiGen\Parser\Reflection\Parts\StartLineEndLine;
use ApiGen\Parser\Reflection\Parts\StartPositionEndPositionMagic;

/**
 * Envelope for magic properties that are defined
 * only as @property, @property-read or @property-write annotation.
 */
class ReflectionPropertyMagic extends ReflectionProperty implements MagicPropertyReflectionInterface
{

    use IsDocumentedMagic;
    use StartLineEndLine;
    use StartPositionEndPositionMagic;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $typeHint;

    /**
     * @var string
     */
    private $shortDescription;

    /**
     * @var string
     */
    private $longDescription;

    /**
     * @var bool
     */
    private $readOnly;

    /**
     * @var bool
     */
    private $writeOnly;

    /**
     * @var ReflectionClass
     */
    private $declaringClass;


    public function __construct(array $options)
    {
        $this->name = $options['name'];
        $this->typeHint = $options['typeHint'];
        $this->shortDescription = $options['shortDescription'];
        $this->startLine = $options['startLine'];
        $this->endLine = $options['endLine'];
        $this->readOnly = $options['readOnly'];
        $this->writeOnly = $options['writeOnly'];
        $this->declaringClass = $options['declaringClass'];
        $this->addAnnotation('var', $options['typeHint']);
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
    public function isWriteOnly()
    {
        return $this->writeOnly;
    }


    /**
     * {@inheritdoc}
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }


    /**
     * {@inheritdoc}
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }


    /**
     * {@inheritdoc}
     */
    public function isReadOnly()
    {
        return $this->readOnly;
    }


    /**
     * {@inheritdoc}
     */
    public function isMagic()
    {
        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function isDeprecated()
    {
        return $this->declaringClass->isDeprecated();
    }


    /**
     * {@inheritdoc}
     */
    public function getPackageName()
    {
        return $this->declaringClass->getPackageName();
    }


    /**
     * {@inheritdoc}
     */
    public function getNamespaceName()
    {
        return $this->declaringClass->getNamespaceName();
    }


    /**
     * {@inheritdoc}
     */
    public function getAnnotations()
    {
        if ($this->annotations === null) {
            $this->annotations = [];
        }
        return $this->annotations;
    }


    /**
     * {@inheritdoc}
     */
    public function getDeclaringClass()
    {
        return $this->declaringClass;
    }


    /**
     * {@inheritdoc}
     */
    public function getDeclaringClassName()
    {
        return $this->declaringClass->getName();
    }


    /**
     * {@inheritdoc}
     */
    public function setDeclaringClass(ClassReflectionInterface $declaringClass)
    {
        $this->declaringClass = $declaringClass;
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getDefaultValue()
    {
        return null;
    }


    /**
     * {@inheritdoc}
     */
    public function isDefault()
    {
        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function isPrivate()
    {
        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function isProtected()
    {
        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function isPublic()
    {
        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function isStatic()
    {
        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function getDeclaringTrait()
    {
        return $this->declaringClass->isTrait() ? $this->declaringClass : null;
    }


    /**
     * {@inheritdoc}
     */
    public function getDeclaringTraitName()
    {
        if ($declaringTrait = $this->getDeclaringTrait()) {
            return $declaringTrait->getName();
        }
        return null;
    }


    /**
     * {@inheritdoc}
     */
    public function getNamespaceAliases()
    {
        return $this->declaringClass->getNamespaceAliases();
    }


    /**
     * {@inheritdoc}
     */
    public function getPrettyName()
    {
        return sprintf('%s::$%s', $this->declaringClass->getName(), $this->name);
    }


    /**
     * {@inheritdoc}
     */
    public function getFileName()
    {
        return $this->declaringClass->getFileName();
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
    public function getDocComment()
    {
        $docComment = "/**\n";

        if (! empty($this->shortDescription)) {
            $docComment .= $this->shortDescription . "\n\n";
        }

        if ($annotations = $this->getAnnotation('var')) {
            $docComment .= sprintf("@var %s\n", $annotations[0]);
        }

        $docComment .= "*/\n";

        return $docComment;
    }


    /**
     * {@inheritdoc}
     */
    public function hasAnnotation($name)
    {
        $annotations = $this->getAnnotations();
        return array_key_exists($name, $annotations);
    }


    /**
     * {@inheritdoc}
     */
    public function getAnnotation($name)
    {
        $annotations = $this->getAnnotations();
        if (array_key_exists($name, $annotations)) {
            return $annotations[$name];
        }
        return null;
    }
}
