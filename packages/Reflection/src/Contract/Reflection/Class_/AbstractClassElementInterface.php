<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Class_;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AccessLevelInterface;

interface AbstractClassElementInterface extends AccessLevelInterface, AbstractReflectionInterface
{
    public function getDeclaringClass(): ClassReflectionInterface;

    public function getDeclaringClassName(): string;
}
