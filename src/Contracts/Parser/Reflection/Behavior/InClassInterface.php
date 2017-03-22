<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;

interface InClassInterface extends InNamespaceInterface
{
    public function getDeclaringClass(): ?ClassReflectionInterface;

    public function getNamespaceName(): string;

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array;

    /**
     * @return string[]
     */
    public function getNamespaceAliases(): array;
}
