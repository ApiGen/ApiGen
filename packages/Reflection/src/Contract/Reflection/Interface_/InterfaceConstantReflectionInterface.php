<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Interface_;

interface InterfaceConstantReflectionInterface extends AbstractInterfaceElementInterface
{
    /**
     * @return mixed
     */
    public function getValue();

    public function getTypeHint(): string;
}
