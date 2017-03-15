<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\InTraitInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\NamedInterface;

interface PropertyReflectionInterface extends
    ElementReflectionInterface,
    InTraitInterface,
    InClassInterface,
    LinedInterface
{

    /**
     * @return bool
     */
    public function isValid();


    /**
     * @return bool
     */
    public function isDefault();


    /**
     * @return bool
     */
    public function isStatic();


    /**
     * @return mixed
     */
    public function getDefaultValue();


    /**
     * @return string
     */
    public function getTypeHint();


    /**
     * @return bool
     */
    public function isMagic();


    /**
     * @return bool
     */
    public function isReadOnly();


    /**
     * @return bool
     */
    public function isWriteOnly();


    /**
     * @param string $name
     * @return bool
     */
    public function hasAnnotation($name);


    /**
     * @param string $name
     * @return bool
     */
    public function getAnnotation($name);
}
