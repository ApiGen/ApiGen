<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\TokenReflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Exception\Parser\Reflection\UnsupportedClassException;
use ApiGen\Parser\Reflection\AbstractReflection;
use ApiGen\ReflectionToElementTransformer\Contract\TransformerCollectorInterface;

/**
 * @todo rename to TransformerCollector
 */
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
        $element = $this->createByReflectionType($reflection);

        $this->setDependencies($element);

        return $element;
    }

    /**
     * @param mixed $reflection
     * @return mixed
     */
    private function createByReflectionType($reflection)
    {
        foreach ($this->transformerCollector->getTransformers() as $transformer) {
            if (! $transformer->matches($reflection)) {
                continue;
            }

            return $transformer->transform($reflection);
        }

        throw new UnsupportedClassException(sprintf(
            'Invalid reflection class "%s". Register new transformer.',
            get_class($reflection)
        ));
    }

    private function setDependencies(AbstractReflection $reflection): void
    {
        $reflection->setConfiguration($this->configuration);
        $reflection->setParserStorage($this->parserStorage);
        $reflection->setReflectionFactory($this);
    }
}
