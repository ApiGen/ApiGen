<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\AbstractFunctionMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicParameterReflectionInterface;
use TokenReflection;

final class ReflectionParameterMagic extends ReflectionParameter implements MagicParameterReflectionInterface
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $typeHint;

    /**
     * @var int
     */
    private $position;

    /**
     * @var string
     */
    private $defaultValueDefinition;

    /**
     * @var bool
     */
    private $unlimited;

    /**
     * @var bool
     */
    private $passedByReference;

    /**
     * @var ReflectionMethodMagic
     */
    private $declaringFunction;


    public function __construct(array $settings)
    {
        $this->name = $settings['name'];
        $this->position = $settings['position'];
        $this->typeHint = $settings['typeHint'];
        $this->defaultValueDefinition = $settings['defaultValueDefinition'];
        $this->unlimited = $settings['unlimited'];
        $this->passedByReference = $settings['passedByReference'];
        $this->declaringFunction = $settings['declaringFunction'];

        $this->reflectionType = get_class($this);
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function getTypeHint(): string
    {
        return $this->typeHint;
    }


    public function getFileName(): string
    {
        return $this->declaringFunction->getFileName();
    }


    public function isTokenized(): bool
    {
        return true;
    }


    public function getPrettyName(): string
    {
        return str_replace('()', '($' . $this->name . ')', $this->declaringFunction->getPrettyName());
    }


    public function getDeclaringClass(): ClassReflectionInterface
    {
        return $this->declaringFunction->getDeclaringClass();
    }


    public function getDeclaringClassName(): string
    {
        return (string) $this->declaringFunction->getDeclaringClassName();
    }


    public function getDeclaringFunction(): AbstractFunctionMethodReflectionInterface
    {
        return $this->declaringFunction;
    }


    public function getDeclaringFunctionName(): string
    {
        return $this->declaringFunction->getName();
    }


    public function getStartLine(): int
    {
        return $this->declaringFunction->getStartLine();
    }


    public function getEndLine(): int
    {
        return $this->declaringFunction->getEndLine();
    }


    public function getDocComment(): string
    {
        return '';
    }


    public function isDefaultValueAvailable(): bool
    {
        return (bool) $this->defaultValueDefinition;
    }

    public function getDefaultValueDefinition(): ?string
    {
        return $this->defaultValueDefinition;
    }


    public function getPosition(): int
    {
        return $this->position;
    }


    public function isArray(): bool
    {
        return TokenReflection\ReflectionParameter::ARRAY_TYPE_HINT === $this->typeHint;
    }


    public function isCallable(): bool
    {
        return TokenReflection\ReflectionParameter::CALLABLE_TYPE_HINT === $this->typeHint;
    }


    public function getClass(): ?ClassReflectionInterface
    {
        $className = (string) $this->getClassName();
        return $className === '' ? null : $this->getParsedClasses()[$className];
    }


    public function getClassName(): ?string
    {
        if ($this->isArray() || $this->isCallable()) {
            return null;
        }

        if (isset($this->getParsedClasses()[$this->typeHint])) {
            return $this->typeHint;
        }

        return null;
    }


    public function allowsNull(): bool
    {
        if ($this->isArray() || $this->isCallable()) {
            return strtolower($this->defaultValueDefinition) === 'null';
        }

        return ! empty($this->defaultValueDefinition);
    }


    public function isOptional(): bool
    {
        return $this->isDefaultValueAvailable();
    }


    public function isPassedByReference(): bool
    {
        return $this->passedByReference;
    }


    public function canBePassedByValue(): bool
    {
        return false;
    }


    public function isUnlimited(): bool
    {
        return $this->unlimited;
    }
}
