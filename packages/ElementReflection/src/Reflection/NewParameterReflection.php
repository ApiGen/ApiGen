<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Reflection;

use ApiGen\Annotation\AnnotationList;
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

    public function getDeclaringFunction(): AbstractFunctionMethodReflectionInterface
    {
        return $this->declaringFunction;
    }

    public function getDeclaringFunctionName(): string
    {
        return $this->declaringFunction->getName();
    }

    public function getTypeHint(): string
    {
        return (string) $this->reflection->getTypeHint();
    }

    public function getDescription(): string
    {
        $annotations = $this->declaringFunction->getAnnotation(AnnotationList::PARAM);
        if (empty($annotations[$this->reflection->getPosition()])) {
            return '';
        }

        $description = trim(strpbrk(
            $annotations[$this->reflection->getPosition()],
            "\n\r\t "
        ));

        return preg_replace(
            '~^(\\$' . $this->getName() . '(?:,\\.{3})?)(\\s+|$)~i', '\\2',
            $description,
            1
        );
    }

    public function getDefaultValueDefinition(): ?string
    {
        // TODO: Implement getDefaultValueDefinition() method.
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

    public function getDeclaringClass(): ?ClassReflectionInterface
    {
        // TODO: Implement getDeclaringClass() method.
    }

    // @todo: rename to variadic
    public function isVariadic(): bool
    {
        return $this->reflection->isVariadic();
    }

    public function setDeclaringFunction(AbstractFunctionMethodReflectionInterface $declaringFunction)
    {
        $this->declaringFunction = $declaringFunction;
    }
}
