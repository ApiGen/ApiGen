<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

interface InNamespaceInterface
{
    public function getDeclaringClassName(): string;

    /**
     * @return string[]
     */
    public function getNamespaceAliases(): array;
}
