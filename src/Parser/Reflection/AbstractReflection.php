<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionInterface;
use ApiGen\ReflectionToElementTransformer\Contract\TransformerCollectorInterface;
use TokenReflection\IReflection;
use TokenReflection\IReflectionClass;
use TokenReflection\IReflectionMethod;
use TokenReflection\IReflectionProperty;

abstract class AbstractReflection implements ReflectionInterface
{
    /**
     * @var IReflectionClass|IReflectionMethod|IReflectionProperty
     */
    protected $reflection;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var ParserStorageInterface
     */
    protected $parserStorage;

    /**
     * @var TransformerCollectorInterface
     */
    protected $transformerCollector;

    public function __construct(IReflection $reflection)
    {
        $this->reflection = $reflection;
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getPrettyName(): string
    {
        return $this->reflection->getPrettyName();
    }

    public function isInternal(): bool
    {
        return $this->reflection->isInternal();
    }

    public function getFileName(): string
    {
        return $this->reflection->getFileName();
    }

    public function getStartLine(): int
    {
        return $this->reflection->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->reflection->getEndLine();
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function setParserStorage(ParserStorageInterface $parserStorage): void
    {
        $this->parserStorage = $parserStorage;
    }

    public function setTransformerCollector(TransformerCollectorInterface $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }

    /**
     * @todo: set called information externally and drop this service locator dependency
     * @return ClassReflectionInterface[]
     */
    public function getParsedClasses(): array
    {
        return $this->parserStorage->getElementsByType(ElementsInterface::CLASSES);
    }
}
