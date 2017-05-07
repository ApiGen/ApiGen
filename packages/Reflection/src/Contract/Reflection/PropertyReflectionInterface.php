<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection;

interface PropertyReflectionInterface extends
    ReflectionInterface
{
    public function isDefault(): bool;

    public function isStatic(): bool;

    /**
     * @return mixed
     */
    public function getDefaultValue();

    public function getTypeHint(): string;

    public function hasAnnotation(string $name): bool;

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array;

    public function getStartLine(): int;

    public function getEndLine(): int;

    public function getDeclaringClass(): ?ClassReflectionInterface;

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array;

    public function getDeclaringClassName(): string;

    public function getNamespaceName(): string;

    // sometimes
    public function getDeclaringTrait(): ?TraitReflectionInterface;

    public function getDeclaringTraitName(): string
}
