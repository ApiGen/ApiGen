<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

interface ClassMethodReflectionInterface extends AbstractMethodReflectionInterface
{
    public function getDeclaringClassName(): string;
}
