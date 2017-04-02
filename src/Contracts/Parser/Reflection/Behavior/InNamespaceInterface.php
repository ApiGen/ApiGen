<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

interface InNamespaceInterface
{
    public function getDeclaringClassName(): string;

    public function getNamespaceName(): string;
}
