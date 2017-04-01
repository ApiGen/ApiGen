<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Reflection;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;

final class NewFunctionReflection implements FunctionReflectionInterface
{
    public function __construct(
        string $name,
        int $startLine,
        int $endLine
    ) {

    }

    public function returnsReference(): bool
    {
        // TODO: Implement returnsReference() method.
    }

    /**
     * @return ParameterReflectionInterface[]
     */
    public function getParameters(): array
    {
        // TODO: Implement getParameters() method.
    }

    public function isDocumented(): bool
    {
        // TODO: Implement isDocumented() method.
    }

    public function isDeprecated(): bool
    {
        // TODO: Implement isDeprecated() method.
    }

    public function getNamespaceName(): string
    {
        // TODO: Implement getNamespaceName() method.
    }

    /**
     * Returns element namespace name.
     * For internal elements returns "PHP", for elements in global space returns "None".
     *
     * @return string
     */
    public function getPseudoNamespaceName(): string
    {
        // TODO: Implement getPseudoNamespaceName() method.
    }

    /**
     * @return string[]
     */
    public function getNamespaceAliases(): array
    {
        // TODO: Implement getNamespaceAliases() method.
    }

    /**
     * Removes the short and long description.
     *
     * @return mixed[]
     */
    public function getAnnotations(): array
    {
        // TODO: Implement getAnnotations() method.
    }

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array
    {
        // TODO: Implement getAnnotation() method.
    }

    public function hasAnnotation(string $name): bool
    {
        // TODO: Implement hasAnnotation() method.
    }

    public function getDescription(): string
    {
        // TODO: Implement getDescription() method.
    }

    /**
     * @return string|bool
     */
    public function getDocComment()
    {
        // TODO: Implement getDocComment() method.
    }

    public function getPrettyName(): string
    {
        // TODO: Implement getPrettyName() method.
    }

    /**
     * Returns the unqualified name (UQN).
     */
    public function getShortName(): string
    {
        // TODO: Implement getShortName() method.
    }

    public function getStartLine(): int
    {
        // TODO: Implement getStartLine() method.
    }

    public function getEndLine(): int
    {
        // TODO: Implement getEndLine() method.
    }

    public function getName(): string
    {
        // TODO: Implement getName() method.
    }
}
