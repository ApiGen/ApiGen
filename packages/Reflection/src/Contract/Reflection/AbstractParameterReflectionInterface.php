<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\InterfaceReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;

interface AbstractParameterReflectionInterface
{
    public function getName(): string;

    public function getTypeHint(): string;

    public function getDescription(): string;

    public function getDefaultValueDefinition(): ?string;

    public function isArray(): bool;

    /**
     * @todo This is actually return parameter typehint. Find a better name.
     *
     * @return ClassReflectionInterface|InterfaceReflectionInterface|null
     */
    public function getClass();

    /**
     * @todo This is actually return parameter typehint. Find a better name.
     */
    public function getClassName(): ?string;

    public function isVariadic(): bool;

    public function isCallable(): bool;
}
