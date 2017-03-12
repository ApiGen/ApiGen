<?php

namespace ApiGen\Parser\Reflection\TokenReflection;

interface ReflectionInterface
{

    /**
     * Returns the name (FQN).
     *
     * @return string
     */
    public function getName();


    /**
     * Returns if the reflection object is internal.
     *
     * @return bool
     */
    public function isInternal();


    /**
     * Returns if the current reflection comes from a tokenized source.
     *
     * @return bool
     */
    public function isTokenized();


    /**
     * Returns an element pretty (docblock compatible) name.
     *
     * @return string
     */
    public function getPrettyName();
}
