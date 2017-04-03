<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use Roave\BetterReflection\Reflection\ReflectionProperty;

final class NewPropertyReflection implements PropertyReflectionInterface
{
    /**
     * @var ReflectionProperty
     */
    private $betterPropertyReflection;

    public function __construct(ReflectionProperty $betterPropertyReflection)
    {
        $this->betterPropertyReflection = $betterPropertyReflection;
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
     * Removes the short and long description.
     *
     * @return mixed[]
     */
    public function getAnnotations(): array
    {
        // TODO: Implement getAnnotations() method.
    }

    public function getDescription(): string
    {
        // TODO: Implement getDescription() method.
    }

    public function getDeclaringClass(): ?ClassReflectionInterface
    {
        // TODO: Implement getDeclaringClass() method.
    }

    public function getDeclaringClassName(): string
    {
        // TODO: Implement getDeclaringClassName() method.
    }

    public function getDeclaringTrait(): ?ClassReflectionInterface
    {
        // TODO: Implement getDeclaringTrait() method.
    }

    public function getDeclaringTraitName(): string
    {
        // TODO: Implement getDeclaringTraitName() method.
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

    public function isDefault(): bool
    {
        // TODO: Implement isDefault() method.
    }

    public function isStatic(): bool
    {
        // TODO: Implement isStatic() method.
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        // TODO: Implement getDefaultValue() method.
    }

    public function getTypeHint(): string
    {
        // TODO: Implement getTypeHint() method.
    }

    public function isReadOnly(): bool
    {
        // TODO: Implement isReadOnly() method.
    }

    public function isWriteOnly(): bool
    {
        // TODO: Implement isWriteOnly() method.
    }

    public function hasAnnotation(string $name): bool
    {
        // TODO: Implement hasAnnotation() method.
    }

    /**
     * @param string $name
     * @return mixed[]
     */
    public function getAnnotation(string $name): array
    {
        // TODO: Implement getAnnotation() method.
    }
}
