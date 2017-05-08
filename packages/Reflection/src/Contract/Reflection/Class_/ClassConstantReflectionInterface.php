<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Class_;

interface ClassConstantReflectionInterface extends AbstractClassElementInterface
{
    public function getTypeHint(): string;

    /**
     * @return mixed
     */
    public function getValue();

    public function getValueDefinition(): string;

    public function getStartLine(): int;

    public function getEndLine(): int;

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array;

    public function getNamespaceName(): string;

    public function getName(): string;
}
