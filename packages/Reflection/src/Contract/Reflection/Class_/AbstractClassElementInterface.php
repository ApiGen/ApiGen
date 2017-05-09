<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Partial\AccessLevelInterface;

interface AbstractClassElementInterface extends AccessLevelInterface
{
    public function getDeclaringClass(): ClassReflectionInterface;

    public function getDeclaringClassName(): string;
}
