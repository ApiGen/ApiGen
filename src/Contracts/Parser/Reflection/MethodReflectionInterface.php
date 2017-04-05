<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\InTraitInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;

interface MethodReflectionInterface extends
    AbstractFunctionMethodReflectionInterface,
    InClassInterface,
    InTraitInterface,
    LinedInterface
{
    public function getPrettyName(): string;

    public function isAbstract(): bool;

    public function isFinal(): bool;

    public function isStatic(): bool;

    public function getImplementedMethod(): ?MethodReflectionInterface;

    public function getOverriddenMethod(): ?MethodReflectionInterface;

    public function getOriginalName(): string;
}
