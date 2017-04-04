<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\NamedInterface;

interface ElementReflectionInterface extends NamedInterface
{
    public function isDocumented(): bool;

    public function isDeprecated(): bool;

    public function getNamespaceName(): string;

    /**
     * Returns element namespace name.
     * For internal elements returns "PHP", for elements in global space returns "None".
     */
    public function getPseudoNamespaceName(): string;

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

    public function getPrettyName(): string;

    /**
     * Returns the unqualified name (UQN).
     */
    public function getShortName(): string;
}
