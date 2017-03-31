<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Parser\Reflection\TokenReflection\ReflectionInterface;

interface ParameterReflectionInterface extends ReflectionInterface
{
    public function getTypeHint(): string;

    public function getDescription(): string;

    public function getDefaultValueDefinition(): ?string;

    public function getPosition(): int;

    public function isArray(): bool;

    public function getClass(): ?ClassReflectionInterface;

    public function getClassName(): ?string;

    public function isOptional(): bool;

    public function canBePassedByValue(): bool;

    public function getDeclaringFunction(): AbstractFunctionMethodReflectionInterface;

    public function getDeclaringFunctionName(): string;

    public function getDeclaringClass(): ?ClassReflectionInterface;

    public function getDeclaringClassName(): string;

    public function isUnlimited(): bool;
}
