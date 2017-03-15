<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Magic;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\NamedInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;

interface MagicMethodReflectionInterface extends MethodReflectionInterface
{

    /**
     * @return bool
     */
    public function isPublic();


    /**
     * @return bool
     */
    public function isProtected();


    /**
     * @return bool
     */
    public function isPrivate();


    /**
     * @return MagicParameterReflectionInterface[]
     */
    public function getParameters();


    /**
     * @param MagicParameterReflectionInterface[] $parameters
     */
    public function setParameters(array $parameters);


    /**
     * @return string
     */
    public function getFileName();


    /**
     * @return bool
     */
    public function isTokenized();
}
