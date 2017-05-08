<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;

interface ClassPropertyReflectionInterface extends AbstractClassElementInterface, StartAndEndLineInterface
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

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array;

    public function getNamespaceName(): string;

    public function getName(): string;

    /**
     * Returns the unqualified name (UQN).
     */
    public function getShortName(): string;

    public function isDocumented(): bool;

    public function isDeprecated(): bool;

    public function getDescription(): string;
}
