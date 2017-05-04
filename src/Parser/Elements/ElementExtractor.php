<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementExtractorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ReflectionInterface;

final class ElementExtractor implements ElementExtractorInterface
{
    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    public function __construct(ElementStorageInterface $elementStorage)
    {
        $this->elementStorage = $elementStorage;
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
            $this->elementStorage->getFunctions(),
            $annotation
        );

        $elements['classes'] = $this->filterByAnnotation(
            $this->elementStorage->getClasses(),
            $annotation
        );

        $elements['interfaces'] = $this->filterByAnnotation(
            $this->elementStorage->getInterfaces(),
            $annotation
        );

        $elements['traits'] = $this->filterByAnnotation(
            $this->elementStorage->getTraits(),
            $annotation
        );

        foreach ($this->elementStorage->getClasses() as $classReflection) {
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
