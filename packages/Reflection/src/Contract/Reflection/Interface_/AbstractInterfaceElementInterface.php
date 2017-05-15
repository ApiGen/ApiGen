<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Interface_;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;

interface AbstractInterfaceElementInterface extends AbstractReflectionInterface
{
    public function getDeclaringInterfaceName(): string;

    public function getDeclaringInterface(): InterfaceReflectionInterface;
}
