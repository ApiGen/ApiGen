<?php

namespace ApiGen\Contracts\Parser;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ArrayObject;

interface ParserStorageInterface
{

    /**
     * @param string $type
     * @return ArrayObject
     */
    public function getElementsByType($type);


    /**
     * Get stats for documented classes, functions and constants.
     *
     * @return array
     */
    public function getDocumentedStats();


    /**
     * @return ArrayObject
     */
    public function getClasses();


    /**
     * @return ArrayObject
     */
    public function getConstants();


    /**
     * @return ArrayObject
     */
    public function getFunctions();


    /**
     * @return string[]
     */
    public function getTypes();


    public function setClasses(ArrayObject $classes);


    public function setConstants(ArrayObject $constants);


    public function setFunctions(ArrayObject $functions);


    public function setInternalClasses(ArrayObject $internalClasses);


    public function setTokenizedClasses(ArrayObject $tokenizedClasses);


    /**
     * @return ClassReflectionInterface[]|array
     */
    public function getDirectImplementersOfInterface(ClassReflectionInterface $reflectionClass);


    /**
     * @return ClassReflectionInterface[]|array
     */
    public function getIndirectImplementersOfInterface(ClassReflectionInterface $reflectionClass);
}
