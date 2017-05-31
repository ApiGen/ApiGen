<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Partial;

use phpDocumentor\Reflection\DocBlock\Tag;

interface AnnotationsInterface
{
    public function isDeprecated(): bool;

    public function getDescription(): string;

    public function hasAnnotation(string $name): bool;

    /**
     * @return Tag[]
     */
    public function getAnnotation(string $name): array;

    /**
     * @return Tag[]|Tag[][]
     */
    public function getAnnotations(): array;
}
