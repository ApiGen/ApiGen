<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;

interface ClassConstantReflectionInterface extends AbstractClassElementInterface, StartAndEndLineInterface,
        AnnotationsInterface
{
    public function getName(): string;

    /**
     * @return mixed
     */
    public function getValue();

    public function getTypeHint(): string;
}
