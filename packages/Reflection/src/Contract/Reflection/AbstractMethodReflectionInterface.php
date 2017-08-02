<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

use ApiGen\Reflection\Contract\Reflection\Partial\AccessLevelInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;

interface AbstractMethodReflectionInterface extends StartAndEndLineInterface, AnnotationsInterface, AccessLevelInterface, AbstractReflectionInterface
{
    public function getShortName(): string;

    /**
     * @return AbstractParameterReflectionInterface[]
     */
    public function getParameters(): array;

    public function isAbstract(): bool;

    public function isFinal(): bool;

    public function isStatic(): bool;

    public function returnsReference(): bool;
}
