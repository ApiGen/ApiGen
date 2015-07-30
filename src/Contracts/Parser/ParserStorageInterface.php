<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ArrayObject;

interface ParserStorageInterface
{

    /**
     * @param string $type
     * @return ArrayObject
     */
    function getElementsByType($type);


    /**
     * Get stats for documented classes, functions and constants.
     *
     * @return array
     */
    function getDocumentedStats();


    /**
     * @return ArrayObject
     */
    function getClasses();


    /**
     * @return ArrayObject
     */
    function getConstants();


    /**
     * @return ArrayObject
     */
    function getFunctions();


    /**
     * @return string[]
     */
    function getTypes();


    function setClasses(ArrayObject $classes);


    function setConstants(ArrayObject $constants);


    function setFunctions(ArrayObject $functions);


    function setInternalClasses(ArrayObject $internalClasses);


    function setTokenizedClasses(ArrayObject $tokenizedClasses);


    /**
     * @return ClassReflectionInterface[]|array
     */
    function getDirectImplementersOfInterface(ClassReflectionInterface $reflectionClass);


    /**
     * @return ClassReflectionInterface[]|array
     */
    function getIndirectImplementersOfInterface(ClassReflectionInterface $reflectionClass);
}
