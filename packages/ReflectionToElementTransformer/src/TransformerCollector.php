<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Parser\Reflection\AbstractReflection;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use ApiGen\ReflectionToElementTransformer\Contract\TransformerCollectorInterface;
use ApiGen\ReflectionToElementTransformer\Exception\UnsupportedReflectionClassException;

final class TransformerCollector implements TransformerCollectorInterface
{
    /**
     * @var TransformerInterface[]
     */
    private $transformers = [];

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var ParserStorageInterface
     */
    private $parserStorage;

    public function __construct(ConfigurationInterface $configuration, ParserStorageInterface $parserStorage)
    {
        $this->configuration = $configuration;
        $this->parserStorage = $parserStorage;
    }

    public function addTransformer(TransformerInterface $transformer): void
    {
        $this->transformers[] = $transformer;
    }

    /**
     * @param object $reflection
     * @return object
     */
    public function transformReflectionToElement($reflection)
    {
        foreach ($this->transformers as $transformer) {
            if (! $transformer->matches($reflection)) {
                continue;
            }

            $element = $transformer->transform($reflection);

            if ($element instanceof AbstractReflection) {
                $this->setDependencies($element);
            }

            return $element;
        }

        throw new UnsupportedReflectionClassException(sprintf(
            'Reflection class "%s" is not yet supported. Register new transformer implementing "%s".',
            get_class($reflection),
            TransformerInterface::class
        ));
    }

    private function setDependencies(AbstractReflection $element): void
    {
        $element->setConfiguration($this->configuration);
        $element->setParserStorage($this->parserStorage);
        $element->setTransformerCollector($this);
    }
}
