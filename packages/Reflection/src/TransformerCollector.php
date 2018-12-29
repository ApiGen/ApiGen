<?php declare(strict_types=1);

namespace ApiGen\Reflection;

use ApiGen\Configuration\Configuration;
use ApiGen\Element\ReflectionCollectorCollector;
use ApiGen\Reflection\Contract\Reflection\Partial\AccessLevelInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Transformer\SortableTransformerInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\Exception\UnsupportedReflectionClassException;

/**
 * Class TransformerCollector.
 */
final class TransformerCollector
{
    /**
     * @var TransformerInterface[]
     */
    private $transformers = [];

    /**
     * @var ReflectionCollectorCollector
     */
    private $reflectionCollectorCollector;

    /** @var Configuration $configuration */
    private $configuration;

    /**
     * TransformerCollector constructor.
     */
    public function __construct(ReflectionCollectorCollector $reflectionCollectorCollector, Configuration $config)
    {
        $this->reflectionCollectorCollector = $reflectionCollectorCollector;
        $this->configuration = $config;
    }

    public function addTransformer(TransformerInterface $transformer): void
    {
        $this->transformers[] = $transformer;
    }

    public function transformGroup(array $reflections): array
    {
        $elements = [];
        foreach ($reflections as $name => $reflection) {
            $transformedReflection = $this->transformSingle($reflection);
            if ($this->shouldSkipReflection($transformedReflection)) {
                continue;
            }

            $elements[$transformedReflection->getName()] = $transformedReflection;
        }

        $matchingTransformer = $this->detectMatchingTransformer($reflections);

        if ($matchingTransformer instanceof SortableTransformerInterface) {
            uasort($elements, function ($firstElement, $secondElement) {
                return strcmp($firstElement->getName(), $secondElement->getName());
            });
        }

        return $elements;
    }

    /**
     * @param object $reflection
     *
     * @return mixed
     *
     * @throws UnsupportedReflectionClassException
     */
    public function transformSingle($reflection)
    {
        foreach ($this->transformers as $transformer) {
            if (! $transformer->matches($reflection)) {
                continue;
            }

            $transformedReflection = $transformer->transform($reflection);

            if ($transformedReflection instanceof TransformerCollectorAwareInterface) {
                $transformedReflection->setTransformerCollector($this);
            }

            if (! $this->shouldSkipReflection($transformedReflection)) {
                $this->reflectionCollectorCollector->processReflection($transformedReflection);
            }

            return $transformedReflection;
        }

        throw new UnsupportedReflectionClassException(sprintf(
            'Reflection class "%s" is not yet supported. Register new transformer implementing "%s".',
            is_object($reflection) ? get_class($reflection) : 'constant',
            TransformerInterface::class
        ));
    }

    /**
     * @param object $transformedReflection
     */
    private function shouldSkipReflection($transformedReflection): bool
    {
        if ($transformedReflection instanceof AnnotationsInterface
            && $transformedReflection->hasAnnotation('internal')
        ) {
            return true;
        }

        if (! $this->hasAllowedAccessLevel($transformedReflection)) {
            return true;
        }

        return false;
    }

    /**
     * @param object[] $reflections
     */
    private function detectMatchingTransformer(array $reflections): ?TransformerInterface
    {
        $reflection = array_shift($reflections);

        foreach ($this->transformers as $transformer) {
            if ($transformer->matches($reflection)) {
                return $transformer;
            }
        }

        return null;
    }

    /**
     * @param object $transformedReflection
     */
    private function hasAllowedAccessLevel($transformedReflection): bool
    {
        if (! $transformedReflection instanceof AccessLevelInterface) {
            return true;
        }

        $visibilityLevels = $this->configuration->getVisibilityLevels();

        $public = $visibilityLevels & \ReflectionProperty::IS_PUBLIC;
        $protected = $visibilityLevels & \ReflectionProperty::IS_PROTECTED;
        $private = $visibilityLevels & \ReflectionProperty::IS_PRIVATE;

        return ($public && $transformedReflection->isPublic())
            || ($protected && $transformedReflection->isProtected())
            || ($private && $transformedReflection->isPrivate());
    }
}
