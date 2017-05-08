<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Interface_;

interface AbstractInterfaceElementInterface
{
    public function getDeclaringInterfaceName(): string;

    public function getDeclaringInterface(): InterfaceReflectionInterface;
}
