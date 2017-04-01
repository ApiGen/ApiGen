<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\TokenReflection;

interface ReflectionInterface
{
    /**
     * Returns the name (FQN).
     */
    public function getName(): string;

    /**
     * Returns if the reflection object is internal.
     */
    public function isInternal(): bool;

    /**
     * Returns if the current reflection comes from a tokenized source.
     */
    public function isTokenized(): bool;

    /**
     * Returns an element pretty (docblock compatible) name.
     */
    public function getPrettyName(): string;
}
