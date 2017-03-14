<?php

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;

interface InClassInterface extends InNamespaceInterface
{

    /**
     * @return ClassReflectionInterface
     */
    public function getDeclaringClass();


    /**
     * @return string
     */
    public function getNamespaceName();


    /**
     * @return array
     */
    public function getAnnotations();


    /**
     * @return string[]
     */
    public function getNamespaceAliases();
}
