<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Magic;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\NamedInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;

interface MagicMethodReflectionInterface extends MethodReflectionInterface
{

    public function isPublic(): bool;


    public function isProtected(): bool;


    public function isPrivate(): bool;


    /**
     * @return MagicParameterReflectionInterface[]
     */
    public function getParameters();


    /**
     * @param MagicParameterReflectionInterface[] $parameters
     */
    public function setParameters(array $parameters): void;


    public function getFileName(): string;


    public function isTokenized(): bool;
}
