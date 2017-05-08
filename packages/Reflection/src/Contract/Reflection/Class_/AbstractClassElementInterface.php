<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Class_;

interface AbstractClassElementInterface
{
    public function getDeclaringClass(): ClassReflectionInterface;

    public function getDeclaringClassName(): string;
}
