<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Interface_;

use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;

final class InterfaceMethodReflection implements InterfaceMethodReflectionInterface
{
    public function getDeclaringInterfaceName(): string
    {
    }

    public function getDeclaringInterface(): InterfaceReflectionInterface
    {
    }
}
