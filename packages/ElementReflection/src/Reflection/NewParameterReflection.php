<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Reflection;

use ApiGen\Contracts\Parser\Reflection\AbstractFunctionMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use Roave\BetterReflection\Reflection\ReflectionParameter;

/**
 * To replace @see \ApiGen\Parser\Reflection\ReflectionParameter
 */
final class NewParameterReflection implements ParameterReflectionInterface
{
    /**
     * @var ReflectionParameter
     */
    private $reflection;

    /**
     * @var AbstractFunctionMethodReflectionInterface
     */
    private $declaringFunction;

    public function __construct(
        ReflectionParameter $betterParameterReflection
    ) {
        $this->reflection = $betterParameterReflection;
    }

    public function getPrettyName(): string
    {
        return str_replace(
            '()',
            '($' . $this->reflection->getName() . ')',
            $this->declaringFunction->getPrettyName()
        );
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }



    public function getTypeHint(): string
    {
        // TODO: Implement getTypeHint() method.
    }

    public function getDescription(): string
    {
        // TODO: Implement getDescription() method.
    }

    public function getDefaultValueDefinition(): ?string
    {
        // TODO: Implement getDefaultValueDefinition() method.
    }

    public function getPosition(): int
    {
        // TODO: Implement getPosition() method.
    }

    public function isArray(): bool
    {
        // TODO: Implement isArray() method.
    }

    public function getClass(): ?ClassReflectionInterface
    {
        // TODO: Implement getClass() method.
    }

    public function getClassName(): ?string
    {
        // TODO: Implement getClassName() method.
    }

    public function getDeclaringClassName(): string
    {
        // TODO: Implement getDeclaringClassName() method.
    }

    public function getDeclaringFunction(): AbstractFunctionMethodReflectionInterface
    {
        // TODO: Implement getDeclaringFunction() method.
    }

    public function getDeclaringFunctionName(): string
    {
        // TODO: Implement getDeclaringFunctionName() method.
    }

    public function getDeclaringClass(): ?ClassReflectionInterface
    {
        // TODO: Implement getDeclaringClass() method.
    }

    // @todo: rename to variadic

    public function isUnlimited(): bool
    {
        // TODO: Implement isUnlimited() method.
    }
    /**
     * Returns if the reflection object is internal.
     */
    public function isInternal(): bool
    {
        // TODO: Implement isInternal() method.
    }

    public function setDeclaringFunction(AbstractFunctionMethodReflectionInterface $declaringFunction)
    {
        $this->declaringFunction = $declaringFunction;
    }
}
