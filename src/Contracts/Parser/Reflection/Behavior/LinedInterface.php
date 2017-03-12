<?php

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

interface LinedInterface
{

    /**
     * @return int
     */
    public function getStartLine();


    /**
     * @return int
     */
    public function getEndLine();
}
