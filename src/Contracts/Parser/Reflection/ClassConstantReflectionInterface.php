<?php

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;

interface ClassConstantReflectionInterface extends ElementReflectionInterface, InClassInterface, LinedInterface
{

    /**
     * @return string
     */
    public function getTypeHint();


    /**
     * @return mixed
     */
    public function getValue();


    /**
     * @return string
     */
    public function getValueDefinition();
}
