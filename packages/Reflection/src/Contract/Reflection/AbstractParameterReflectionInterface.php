<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

interface AbstractParameterReflectionInterface extends AbstractReflectionInterface
{
    public function getTypeHint(): string;

    public function isDefaultValueAvailable(): bool;

    /**
     * @return mixed
     */
    public function getDefaultValue();

    public function isArray(): bool;

    public function isVariadic(): bool;

    public function isCallable(): bool;

    public function isPassedByReference(): bool;

    public function getDescription(): string;
}
