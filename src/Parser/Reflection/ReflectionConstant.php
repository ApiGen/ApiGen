<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use Exception;

final class ReflectionConstant extends AbstractReflectionElement implements ConstantReflectionInterface
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
        $annotations = $this->getAnnotation('var');

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
}
