<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection;

interface ParameterReflectionInterface
{
    /**
     * Returns the name (FQN).
     */
    public function getName(): string;

    public function getTypeHint(): string;

    public function getDescription(): string;

    public function getDefaultValueDefinition(): ?string;

    public function isArray(): bool;

    /**
     * @todo This is actually return parameter typehint. Find a better name.
     */
    public function getClass(): ?ClassReflectionInterface;

    /**
     * @todo This is actually return parameter typehint. Find a better name.
     */
    public function getClassName(): ?string;

    public function getDeclaringClassName(): string;

    /**
     * @return MethodReflectionInterface|FunctionReflectionInterface
     */
    public function getDeclaringFunction();

    public function getDeclaringFunctionName(): string;

    public function getDeclaringClass(): ?ClassReflectionInterface;

    public function isVariadic(): bool;

    public function isCallable(): bool;

    /**
     * @param MethodReflectionInterface|FunctionReflectionInterface $declaringFunction
     */
    public function setDeclaringFunction($declaringFunction): void;
}
