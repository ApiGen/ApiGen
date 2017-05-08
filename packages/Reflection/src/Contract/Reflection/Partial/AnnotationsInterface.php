<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Partial;

interface AnnotationsInterface
{
    public function getDescription(): string;

    public function hasAnnotation(string $name): bool;

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array;

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array;
}
