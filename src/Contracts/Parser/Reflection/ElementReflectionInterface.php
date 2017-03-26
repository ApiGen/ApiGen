<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\NamedInterface;

interface ElementReflectionInterface extends NamedInterface
{
    public function isMain(): bool;

    public function isDocumented(): bool;

    public function isDeprecated(): bool;

    public function inNamespace(): bool;

    public function getNamespaceName(): string;

    /**
     * Returns element namespace name.
     * For internal elements returns "PHP", for elements in global space returns "None".
     *
     * @return string
     */
    public function getPseudoNamespaceName(): string;

    /**
     * @return string[]
     */
    public function getNamespaceAliases(): array;

    /**
     * Removes the short and long description.
     *
     * @return mixed[]
     */
    public function getAnnotations(): array;

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array;

    public function hasAnnotation(string $name): bool;

    public function getShortDescription(): string;

    public function getLongDescription(): string;

    /**
     * @return string|bool
     */
    public function getDocComment();

    public function getPrettyName(): string;

    /**
     * Returns the unqualified name (UQN).
     */
    public function getShortName(): string;

    public function getStartPosition(): int;

    public function getEndPosition(): int;
}
