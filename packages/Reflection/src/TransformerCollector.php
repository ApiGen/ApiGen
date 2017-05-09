<?php declare(strict_types=1);

namespace ApiGen\Reflection;

use ApiGen\Reflection\Contract\Reflection\Partial\AccessLevelInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use ApiGen\Reflection\Exception\UnsupportedReflectionClassException;

final class TransformerCollector implements TransformerCollectorInterface
{
    /**
     * @var TransformerInterface[]
     */
    private $transformers = [];

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
        foreach ($reflections as $name => $reflection) {
            // also ! $this->reflection->isInternal();, remove isDocumented()

            $transformedReflection = $this->transformSingle($reflection);
            if ($transformedReflection->hasAnnotation('internal')) {
                continue;
            }

            // $this->configuration->getVisibilityLevels()
            // @todo: here is the place to filter out public/protected etc - use service!
            if (! $this->hasAllowedAccessLevel($transformedReflection)) {
                continue;
            }

            $name = $name ?: $transformedReflection->getName();
            $elements[$name] = $transformedReflection;
        }

        // @todo: sort here!, before ElementSorter
        uasort($elements, function ($firstElement, $secondElement) {
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

            if ($element instanceof TransformerCollectorAwareInterface) {
                $element->setTransformerCollector($this);
            }

            return $element;
        }

        throw new UnsupportedReflectionClassException(sprintf(
            'Reflection class "%s" is not yet supported. Register new transformer implementing "%s".',
            is_object($reflection) ? get_class($reflection) : 'constant',
            TransformerInterface::class
        ));
    }

    private function hasAllowedAccessLevel($transformedReflection): bool
    {
        if ( ! $transformedReflection instanceof AccessLevelInterface) {
            return true;
        }

        // hardcoded @todo make service-like and using ConfigurationInterface
        if ($transformedReflection->isPublic() || $transformedReflection->isProtected()) {
            return true;
        }

        return false;
    }
}
