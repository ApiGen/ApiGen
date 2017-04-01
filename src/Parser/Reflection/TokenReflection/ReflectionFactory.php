<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\TokenReflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Reflection\AbstractReflection;
use ApiGen\ReflectionToElementTransformer\Contract\TransformerCollectorInterface;

final class ReflectionFactory implements ReflectionFactoryInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var ParserStorageInterface
     */
    private $parserStorage;

    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

    public function __construct(
        ConfigurationInterface $configuration,
        ParserStorageInterface $parserStorage,
        TransformerCollectorInterface $transformerCollector
    ) {
        $this->configuration = $configuration;
        $this->parserStorage = $parserStorage;
        $this->transformerCollector = $transformerCollector;
    }

    /**
     * @param mixed $reflection
     * @return mixed
     */
    public function createFromReflection($reflection)
    {
        $element = $this->transformerCollector->transformReflectionToElement($reflection);

        $this->setDependencies($element);

        return $element;
    }

    private function setDependencies(AbstractReflection $reflection): void
    {
        $reflection->setConfiguration($this->configuration);
        $reflection->setParserStorage($this->parserStorage);
        $reflection->setReflectionFactory($this);
    }
}
