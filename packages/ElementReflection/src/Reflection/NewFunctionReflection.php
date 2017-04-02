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
    private $reflection;

    /**
     * @var ParameterReflectionInterface[]
     */
    private $parameterReflections = [];

    /**
     * @param ReflectionFunction $reflection
     * @param ParameterReflectionInterface[] $parameterReflections
     */
    public function __construct(ReflectionFunction $reflection, array $parameterReflections = [])
    {
        $this->reflection = $reflection;
        $this->parameterReflections = $parameterReflections;
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getShortName(): string
    {
        return $this->reflection->getShortName();
    }

    public function getStartLine(): int
    {
        return $this->reflection->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->reflection->getEndLine();
    }

    public function returnsReference(): bool
    {
        return $this->reflection->returnsReference();
    }

    public function isDeprecated(): bool
    {
        return $this->reflection->isDeprecated();
    }

    public function getNamespaceName(): string
    {
        return $this->reflection->getNamespaceName();
    }

    public function getPseudoNamespaceName(): string
    {
        if ($this->reflection->isInternal()) {
            return 'PHP';
        }

        if ($this->reflection->getNamespaceName()) {
            return $this->reflection->getNamespaceName();
        }

        return 'None';
    }

    public function getPrettyName(): string
    {
        return $this->reflection->getName() . '()';
    }

    /**
     * @return ParameterReflectionInterface[]
     */
    public function getParameters(): array
    {
        return $this->parameterReflections;
    }

    public function isDocumented(): bool
    {
        // TODO: Implement isDocumented() method.
    }

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array
    {
        // TODO: Implement getAnnotations() method.
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
     * @return mixed[]
     */
    public function getAnnotation(string $name): array
    {
        // TODO: Implement getAnnotation() method.
    }
}
