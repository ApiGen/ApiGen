<?php

namespace ApiGen\Contracts\Parser\Reflection\Magic;

use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;

interface MagicParameterReflectionInterface extends ParameterReflectionInterface
{

    /**
     * @return string
     */
    public function getDocComment();


    /**
     * @return int
     */
    public function getStartLine();


    /**
     * @return int
     */
    public function getEndLine();


    /**
     * @return string
     */
    public function getFileName();


    /**
     * @return bool
     */
    public function isTokenized();
}
