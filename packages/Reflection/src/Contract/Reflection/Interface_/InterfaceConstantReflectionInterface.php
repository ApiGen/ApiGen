<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Interface_;

use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;

interface InterfaceConstantReflectionInterface extends AbstractInterfaceElementInterface, StartAndEndLineInterface
{
    public function getName(): string;

    /**
     * @return mixed
     */
    public function getValue();

    public function getTypeHint(): string;
}
