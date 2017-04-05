<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use Exception;

final class ReflectionProperty extends AbstractReflectionElement implements PropertyReflectionInterface
{
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

    public function isReadOnly(): bool
    {
        return false;
    }

    public function isWriteOnly(): bool
    {
        return false;
    }

    public function getTypeHint(): string
    {
        $annotations = $this->getAnnotation('var');

        if ($annotations) {
            [$types] = preg_split('~\s+|$~', $annotations[0], 2);
            if (! empty($types) && $types[0] !== '$') {
                return $types;
            }
        }

        try {
            $type = gettype($this->getDefaultValue());
            if (strtolower($type) !== 'null') {
                return $type;
            }
        } catch (Exception $exception) {
            return '';
        }
    }

    public function getDeclaringClass(): ?ClassReflectionInterface
    {
        $className = $this->reflection->getDeclaringClassName();
        return $className === null ? null : $this->getParsedClasses()[$className];
    }

    public function getDeclaringClassName(): string
    {
        return (string) $this->reflection->getDeclaringClassName();
    }

    public function getDefaultValueDefinition(): string
    {
        return $this->reflection->getDefaultValueDefinition();
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->reflection->getDefaultValue();
    }

    public function isDefault(): bool
    {
        return $this->reflection->isDefault();
    }

    public function isStatic(): bool
    {
        return $this->reflection->isStatic();
    }

    public function getDeclaringTrait(): ?ClassReflectionInterface
    {
        $traitName = (string) $this->reflection->getDeclaringTraitName();
        return $traitName === '' ? null : $this->getParsedClasses()[$traitName];
    }

    public function getDeclaringTraitName(): string
    {
        return (string) $this->reflection->getDeclaringTraitName();
    }

    public function getShortName(): string
    {
        return $this->getName();
    }
}
