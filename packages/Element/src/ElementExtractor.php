<?php declare(strict_types=1);

namespace ApiGen\Element;

use ApiGen\Reflection\Contract\Reflection\ReflectionInterface;
use ApiGen\Element\Contract\ElementExtractorInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;

final class ElementExtractor implements ElementExtractorInterface
{
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    public function __construct(ReflectionStorageInterface $reflectionStorage)
    {
        $this->reflectionStorage = $reflectionStorage;
    }

    /**
     * @return mixed[]
     */
    public function extractElementsByAnnotation(string $annotation): array
    {
        $elements = [
            // these might be empty, if no classes
            'methods' => [],
            'constants' => [],
            'properties' => [],
        ];

        $elements['functions'] = $this->filterByAnnotation(
            $this->reflectionStorage->getFunctionReflections(),
            $annotation
        );

        $elements['classes'] = $this->filterByAnnotation(
            $this->reflectionStorage->getClassReflections(),
            $annotation
        );

        $elements['interfaces'] = $this->filterByAnnotation(
            $this->reflectionStorage->getInterfaceReflections(),
            $annotation
        );

        $elements['traits'] = $this->filterByAnnotation(
            $this->reflectionStorage->getTraitReflections(),
            $annotation
        );

        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            $elements['methods'] = $this->extractByAnnotationAndMerge(
                $classReflection->getOwnMethods(),
                $annotation,
                $elements['methods']
            );
            $elements['constants'] = $this->extractByAnnotationAndMerge(
                $classReflection->getOwnConstants(),
                $annotation,
                $elements['constants']
            );
            $elements['properties'] = $this->extractByAnnotationAndMerge(
                $classReflection->getOwnProperties(),
                $annotation,
                $elements['properties']
            );
        }

        return $elements;
    }

    /**
     * @param mixed[] $elements
     * @param mixed[] $storage
     * @return mixed[]
     */
    private function extractByAnnotationAndMerge(array $elements, string $annotation, array $storage): array
    {
        $foundElements = $this->filterByAnnotation($elements, $annotation);

        return array_merge($storage, array_values($foundElements));
    }

    /**
     * @param ReflectionInterface[] $elements
     * @return ReflectionInterface[]
     */
    private function filterByAnnotation(array $elements, string $annotation): array
    {
        return array_filter($elements, function (ReflectionInterface $element) use ($annotation) {
            return $element->hasAnnotation($annotation);
        });
    }
}
