<?php declare(strict_types=1);

namespace ApiGen\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Parser\Reflection\AbstractReflection;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use ApiGen\Reflection\Exception\UnsupportedReflectionClassException;

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

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function addTransformer(TransformerInterface $transformer): void
    {
        $this->transformers[] = $transformer;
    }

    /**
     * @param object[] $reflections
     * @return object[]
     */
    public function transformGroup(array $reflections): array
    {
        $elements = [];
        foreach ($reflections as $reflection) {
            // $this->configuration->getVisibilityLevels()
            // @todo: here is the place to filter out public/protected etc
            // also ! $this->reflection->isInternal();, remove isDocumented()
            $elements[] = $this->transformSingle($reflection);

        }

        // @todo: sort here!, before ElementSorter
        usort($elements, function ($firstElement, $secondElement) {
           return strcmp($firstElement->getName(), $secondElement->getName());
        });

        return $elements;
    }

    /**
     * @param object $reflection
     * @return object
     */
    public function transformSingle($reflection)
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
        $element->setTransformerCollector($this);
    }
}
