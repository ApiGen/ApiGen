<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Interface_;

use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;

/**
 * @inspiration https://github.com/POPSuL/PHP-Token-Reflection/blob/develop/TokenReflection/Php/ReflectionConstant.php
 */
final class InterfaceConstantReflection implements InterfaceConstantReflectionInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var InterfaceReflectionInterface
     */
    private $interfaceReflection;

    /**
     * @param mixed $value
     */
    private function __construct(string $name, $value, InterfaceReflectionInterface $interfaceReflection)
    {
        $this->name = $name;
        $this->value = $value;
        $this->interfaceReflection = $interfaceReflection;
    }

    /**
     * @param mixed $value
     */
    public static function createFromNameValueAndInterface(string $name, $value, InterfaceReflectionInterface $interfaceReflection): self
    {
        return new self($name, $value, $interfaceReflection);
    }

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
        return $this->name;
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

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
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

    public function getDeclaringInterfaceName(): string
    {
        $this->interfaceReflection->getName();
    }

    public function getDeclaringInterface(): InterfaceReflectionInterface
    {
        return $this->interfaceReflection;
    }
}
