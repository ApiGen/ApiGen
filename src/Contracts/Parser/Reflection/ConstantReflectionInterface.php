<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection;

interface ConstantReflectionInterface extends ReflectionInterface
{
    public function getTypeHint(): string;

    /**
     * @return mixed
     */
    public function getValue();

    public function getValueDefinition(): string;

    public function getStartLine(): int;

    public function getEndLine(): int;

    public function getDeclaringClass(): ?ClassReflectionInterface;

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array;

    public function getDeclaringClassName(): string;

    public function getNamespaceName(): string;
}
