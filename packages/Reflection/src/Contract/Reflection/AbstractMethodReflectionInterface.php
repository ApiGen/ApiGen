<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

interface AbstractMethodReflectionInterface
{
    public function getNamespaceName(): string;

    public function isAbstract(): bool;

    public function isFinal(): bool;

    public function isStatic(): bool;

    public function getImplementedMethod(): ?ClassMethodReflectionInterface;

    public function getOverriddenMethod(): ?ClassMethodReflectionInterface;

    public function returnsReference(): bool;

    /**
     * @return ParameterReflectionInterface[]
     */
    public function getParameters(): array;

    public function getStartLine(): int;

    public function getEndLine(): int;

    public function getName(): string;

    /**
     * Returns the unqualified name (UQN).
     */
    public function getShortName(): string;

    public function isDocumented(): bool;

    public function isDeprecated(): bool;

    /**
     *
     * @return mixed[]
     */
    public function getAnnotations(): array;

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array;

    public function hasAnnotation(string $name): bool;

    public function getDescription(): string;
}
