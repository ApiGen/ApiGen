<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Function_;

use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;

interface FunctionReflectionInterface extends StartAndEndLineInterface
{
    public function returnsReference(): bool;

    /**
     * @return FunctionParameterReflectionInterface[]
     */
    public function getParameters(): array;

    public function getName(): string;

    public function getShortName(): string;

    public function isDeprecated(): bool;

    public function getNamespaceName(): string;

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array;

    public function hasAnnotation(string $name): bool;

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array;

    public function getDescription(): string;

    public function isDocumented(): bool;

    public function getFileName(): string;
}
