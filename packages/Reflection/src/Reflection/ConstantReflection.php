<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;

// @todo, resolve manually probably
final class ConstantReflection implements ConstantReflectionInterface
{
//    public function __construct()
//    {
//        new Reflection
//    }

    public function isPrivate(): bool
    {
        return $this->reflection->isPrivate();
    }

    public function isProtected(): bool
    {
        return $this->reflection->isProtected();
    }

    public function isPublic(): bool
    {
        return $this->reflection->isPublic();
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getShortName(): string
    {
        return $this->reflection->getShortName();
    }

    public function getTypeHint(): string
    {
        $annotations = $this->getAnnotation(AnnotationList::VAR_);

        if ($annotations) {
            [$types] = preg_split('~\s+|$~', $annotations[0], 2);
            if (! empty($types)) {
                return $types;
            }
        }

        try {
            $type = gettype($this->getValue());
            if (strtolower($type) !== 'null') {
                return $type;
            }
        } catch (Exception $exception) {
            return '';
        }
    }

    public function getDeclaringClass(): ?ClassReflectionInterface
    {
        $className = (string) $this->reflection->getDeclaringClassName();
        return $this->getParsedClasses()[$className];
    }

    public function getDeclaringClassName(): string
    {
        return (string) $this->reflection->getDeclaringClassName();
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->reflection->getValue();
    }

    public function getValueDefinition(): string
    {
        return $this->reflection->getValueDefinition();
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

    public function getStartLine(): int
    {
        // TODO: Implement getStartLine() method.
    }

    public function getEndLine(): int
    {
        // TODO: Implement getEndLine() method.
    }
}
