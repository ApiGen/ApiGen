<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Reflection;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Webmozart\Assert\Assert;

/**
 * To replace @see \ApiGen\Parser\Reflection\ReflectionFunction
 */
final class NewFunctionReflection implements FunctionReflectionInterface
{
    /**
     * @var string
     */
    private const EMPTY_LINE = PHP_EOL . PHP_EOL;

    /**
     * @var ReflectionFunction
     */
    private $reflection;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var ParameterReflectionInterface[]
     */
    private $parameterReflections = [];

    /**
     * @param ReflectionFunction $betterFunctionReflection
     * @param DocBlock $docBlock
     * @param ParameterReflectionInterface[] $parameterReflections
     */
    public function __construct(
        ReflectionFunction $betterFunctionReflection,
        DocBlock $docBlock,
        array $parameterReflections
    ) {
        $this->reflection = $betterFunctionReflection;
        $this->docBlock = $docBlock;
        $this->setParameterReflections($parameterReflections);
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
     * @return mixed[]
     */
    public function getAnnotations(): array
    {
        return $this->docBlock->getTags();
    }

    public function hasAnnotation(string $name): bool
    {
        return $this->docBlock->hasTag($name);
    }

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array
    {
        return $this->docBlock->getTagsByName($name);
    }

    public function getDescription(): string
    {
        $description = $this->docBlock->getSummary()
            . self::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
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
        if ($this->reflection->isInternal()) {
            return false;
        }

        if ($this->hasAnnotation('internal')) {
            return false;
        }

        return true;
    }

    /**
     * @param ParameterReflectionInterface[] $parameterReflections
     */
    private function setParameterReflections(array $parameterReflections): void
    {
        Assert::allIsInstanceOf($parameterReflections, ParameterReflectionInterface::class);
        $this->parameterReflections = $parameterReflections;
    }
}
