<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Interface_;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;

interface AbstractInterfaceElementInterface extends AbstractReflectionInterface, StartAndEndLineInterface
{
    public function getDeclaringInterfaceName(): string;

    public function getDeclaringInterface(): InterfaceReflectionInterface;
}
