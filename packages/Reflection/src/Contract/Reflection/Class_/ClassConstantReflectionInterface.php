<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;

interface ClassConstantReflectionInterface extends AbstractClassElementInterface, StartAndEndLineInterface
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

    public function getName(): string;
}
