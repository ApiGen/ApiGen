<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Class_;

interface ClassConstantReflectionInterface
{
    public function getTypeHint(): string;

    /**
     * @return mixed
     */
    public function getValue();

    public function getValueDefinition(): string;

    public function getStartLine(): int;

    public function getEndLine(): int;

    public function getDeclaringClass(): ClassReflectionInterface;

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array;

    public function getDeclaringClassName(): string;

    public function getNamespaceName(): string;

    public function getName(): string;
}
