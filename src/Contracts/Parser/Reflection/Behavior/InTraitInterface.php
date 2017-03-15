<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;

interface InTraitInterface extends InNamespaceInterface
{

    /**
     * @return ClassReflectionInterface
     */
    public function getDeclaringTrait();


    /**
     * @return string|NULL
     */
    public function getDeclaringTraitName();
}
