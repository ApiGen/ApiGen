<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Reflection\Contract\Reflection\FunctionParameterReflectionInterface;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;

interface FunctionReflectionInterface
{
    public function getName(): string;

    public function getNamespaceName(): string;

    public function getStartLine(): int;

    public function getEndLine(): int;

    public function getFileName(): string;

    public function returnsReference(): bool;

    /**
     * @return FunctionParameterReflectionInterface[]
     */
    public function getParameters(): array;

    public function getShortName(): string;

    public function getAnnotations(): array;

    public function hasAnnotation(string $name): bool;

    /**
     * @return BaseTag[]
     */
    public function getAnnotation(string $name): array;

    public function isDeprecated(): bool;

    public function getDescription(): string;

    public function isDocumented(): bool;
}
