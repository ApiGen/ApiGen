<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Reflection;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use Roave\BetterReflection\Reflection\ReflectionFunction;

/**
 * To replace @see \ApiGen\Parser\Reflection\ReflectionFunction
 */
final class NewFunctionReflection implements FunctionReflectionInterface
{
    /**
     * @var ReflectionFunction
     */
    private $betterReflectionFunction;

    public function __construct(ReflectionFunction $betterReflectionFunction)
    {
        $this->betterReflectionFunction = $betterReflectionFunction;
    }

    public function getName(): string
    {
        return $this->betterReflectionFunction->getName();
    }

    public function getStartLine(): int
    {
        return $this->betterReflectionFunction->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->betterReflectionFunction->getEndLine();
    }

    public function returnsReference(): bool
    {
        return $this->betterReflectionFunction->returnsReference();
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
}
