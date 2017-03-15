<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Elements;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;

interface ElementStorageInterface
{

    /**
     * @return array
     */
    public function getNamespaces();


    /**
     * @return ClassReflectionInterface[]
     */
    public function getClasses();


    /**
     * @return ClassReflectionInterface[]
     */
    public function getInterfaces();


    /**
     * @return ClassReflectionInterface[]
     */
    public function getTraits();


    /**
     * @return ClassReflectionInterface[]
     */
    public function getExceptions();


    /**
     * @return ConstantReflectionInterface[]
     */
    public function getConstants();


    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctions();


    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassElements();


    /**
     * @return array[]
     */
    public function getElements();
}
