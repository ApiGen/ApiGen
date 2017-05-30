<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;

interface AbstractParameterReflectionInterface extends AbstractReflectionInterface
{
    public function getTypeHint(): string;

    /**
     * @return ClassReflectionInterface|InterfaceReflectionInterface|null
     */
    public function getTypeHintClassOrInterfaceReflection();

    public function getDefaultValueDefinition(): ?string;

    public function isArray(): bool;

    public function isVariadic(): bool;

    public function isCallable(): bool;

    public function getDescription(): string;
}
