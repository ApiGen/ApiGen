<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

interface FunctionReflectionInterface
{
    public function returnsReference(): bool;

    /**
     * @return FunctionParameterReflectionInterface[]
     */
    public function getParameters(): array;

    public function getName(): string;

    public function getShortName(): string;

    public function getStartLine(): int;

    public function getEndLine(): int;

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
