<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Interface_;

use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;

interface InterfaceConstantReflectionInterface extends AbstractInterfaceElementInterface, StartAndEndLineInterface
{
    public function getTypeHint(): string;

    /**
     * @return mixed
     */
    public function getValue();

    public function getValueDefinition(): string;

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array;

    public function getNamespaceName(): string;

    public function getName(): string;
}
