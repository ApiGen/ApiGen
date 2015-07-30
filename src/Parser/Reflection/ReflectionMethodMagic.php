<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Parser\Reflection\Parts\IsDocumentedMagic;
use ApiGen\Parser\Reflection\Parts\StartLineEndLine;
use ApiGen\Parser\Reflection\Parts\StartPositionEndPositionMagic;

class ReflectionMethodMagic extends ReflectionMethod implements MagicMethodReflectionInterface
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
    private $shortDescription;

    /**
     * @var bool
     */
    private $returnsReference;

    /**
     * @var ReflectionClass
     */
    private $declaringClass;

    /**
     * @var bool
     */
    private $static;


    public function __construct(array $settings)
    {
        $this->name = $settings['name'];
        $this->shortDescription = $settings['shortDescription'];
        $this->startLine = $settings['startLine'];
        $this->endLine = $settings['endLine'];
        $this->returnsReference = $settings['returnsReference'];
        $this->declaringClass = $settings['declaringClass'];
        $this->annotations = $settings['annotations'];
        $this->static = isset($settings['static']) ? $settings['static'] : false;

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
    public function getShortDescription()
    {
        return $this->shortDescription;
    }


    /**
     * {@inheritdoc}
     */
    public function getLongDescription()
    {
        return $this->shortDescription;
    }


    /**
     * @return bool
     */
    public function returnsReference()
    {
        return $this->returnsReference;
    }


    /**
     * @return bool
     */
    public function isMagic()
    {
        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function getShortName()
    {
        return $this->name;
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
     * @return bool
     */
    public function isAbstract()
    {
        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function isFinal()
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
        return $this->static;
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
    public function getOriginalName()
    {
        return $this->getName();
    }


    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }


    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
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
        return sprintf('%s::%s()', $this->declaringClass->getName(), $this->name);
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

        if ($annotations = $this->getAnnotation('param')) {
            foreach ($annotations as $annotation) {
                $docComment .= sprintf("@param %s\n", $annotation);
            }
        }

        if ($annotations = $this->getAnnotation('return')) {
            foreach ($annotations as $annotation) {
                $docComment .= sprintf("@return %s\n", $annotation);
            }
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
