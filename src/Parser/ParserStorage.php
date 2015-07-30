<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser;

use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ArrayObject;

class ParserStorage implements ParserStorageInterface
{

    /**
     * @var ArrayObject
     */
    private $classes;

    /**
     * @var ArrayObject
     */
    private $constants;

    /**
     * @var ArrayObject
     */
    private $functions;

    /**
     * @var ArrayObject
     */
    private $internalClasses;

    /**
     * @var ArrayObject
     */
    private $tokenizedClasses;

    /**
     * @var array
     */
    private $types = [ElementsInterface::CLASSES, ElementsInterface::CONSTANTS, ElementsInterface::FUNCTIONS];


    public function __construct()
    {
        $this->classes = new ArrayObject;
        $this->constants = new ArrayObject;
        $this->functions = new ArrayObject;
        $this->internalClasses = new ArrayObject;
        $this->tokenizedClasses = new ArrayObject;
    }


    /**
     * {@inheritdoc}
     */
    public function getElementsByType($type)
    {
        if ($type === ElementsInterface::CLASSES) {
            return $this->classes;

        } elseif ($type === ElementsInterface::CONSTANTS) {
            return $this->constants;

        } elseif ($type === ElementsInterface::FUNCTIONS) {
            return $this->functions;
        }

        throw new \Exception(sprintf(
            '"%s" is not supported element type',
            $type
        ));
    }


    /**
     * {@inheritdoc}
     */
    public function getDocumentedStats()
    {
        return [
            'classes' => $this->getDocumentedElementsCount($this->tokenizedClasses),
            'constants' => $this->getDocumentedElementsCount($this->constants),
            'functions' => $this->getDocumentedElementsCount($this->functions),
            'internalClasses' => $this->getDocumentedElementsCount($this->internalClasses)
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function getClasses()
    {
        return $this->classes;
    }


    /**
     * {@inheritdoc}
     */
    public function getConstants()
    {
        return $this->constants;
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return $this->functions;
    }


    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return $this->types;
    }


    /**
     * {@inheritdoc}
     */
    public function setClasses(ArrayObject $classes)
    {
        $this->classes = $classes;
    }


    /**
     * {@inheritdoc}
     */
    public function setConstants(ArrayObject $constants)
    {
        $this->constants = $constants;
    }


    /**
     * {@inheritdoc}
     */
    public function setFunctions(ArrayObject $functions)
    {
        $this->functions = $functions;
    }


    /**
     * {@inheritdoc}
     */
    public function setInternalClasses(ArrayObject $internalClasses)
    {
        $this->internalClasses = $internalClasses;
    }


    /**
     * {@inheritdoc}
     */
    public function setTokenizedClasses(ArrayObject $tokenizedClasses)
    {
        $this->tokenizedClasses = $tokenizedClasses;
    }


    /**
     * {@inheritdoc}
     */
    public function getDirectImplementersOfInterface(ClassReflectionInterface $reflectionClass)
    {
        $implementers = [];
        foreach ($this->classes as $class) {
            if ($this->isAllowedDirectImplementer($class, $reflectionClass->getName())) {
                $implementers[] = $class;
            }
        }
        uksort($implementers, 'strcasecmp');
        return $implementers;
    }


    /**
     * {@inheritdoc}
     */
    public function getIndirectImplementersOfInterface(ClassReflectionInterface $reflectionClass)
    {
        $implementers = [];
        foreach ($this->classes as $class) {
            if ($this->isAllowedIndirectImplementer($class, $reflectionClass->getName())) {
                $implementers[] = $class;
            }
        }
        uksort($implementers, 'strcasecmp');
        return $implementers;
    }


    /**
     * @param ClassReflectionInterface $class
     * @param string $name
     * @return bool
     */
    private function isAllowedDirectImplementer(ClassReflectionInterface $class, $name)
    {
        if ($class->isDocumented() && in_array($name, $class->getOwnInterfaceNames())) {
            return true;
        }
        return false;
    }


    /**
     * @param ClassReflectionInterface $class
     * @param string $name
     * @return bool
     */
    private function isAllowedIndirectImplementer(ClassReflectionInterface $class, $name)
    {
        if ($class->isDocumented() && $class->implementsInterface($name)
            && ! in_array($name, $class->getOwnInterfaceNames())
        ) {
            return true;
        }
        return false;
    }


    /**
     * @param ElementReflectionInterface[]|ArrayObject $result
     * @return int
     */
    private function getDocumentedElementsCount(ArrayObject $result)
    {
        $count = 0;
        foreach ($result as $element) {
            $count += (int) $element->isDocumented();
        }
        return $count;
    }
}
