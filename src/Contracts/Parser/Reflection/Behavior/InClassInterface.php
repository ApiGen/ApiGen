<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;

interface InClassInterface extends InNamespaceInterface
{
    public function getDeclaringClass(): ?ClassReflectionInterface;

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array;
}
