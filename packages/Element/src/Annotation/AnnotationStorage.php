<?php declare(strict_types=1);

namespace ApiGen\Element\Annotation;

use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;

final class AnnotationStorage
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
     * @return AnnotationsInterface[][]
     */
    public function findByAnnotation(string $annotation): array
    {
        $elements = [
            // these might be empty, if no classes
            'methods' => [],
            'constants' => [],
            'properties' => [],
        ];

        $elements['functions'] = $this->filterReflectionsByAnnotation(
            $this->reflectionStorage->getFunctionReflections(),
            $annotation
        );

        $elements['classes'] = $this->filterReflectionsByAnnotation(
            $this->reflectionStorage->getClassReflections(),
            $annotation
        );

        $elements['interfaces'] = $this->filterReflectionsByAnnotation(
            $this->reflectionStorage->getInterfaceReflections(),
            $annotation
        );

        $elements['traits'] = $this->filterReflectionsByAnnotation(
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
        $foundElements = $this->filterReflectionsByAnnotation($elements, $annotation);

        return array_merge($storage, array_values($foundElements));
    }

    /**
     * @param AnnotationsInterface[] $reflections
     * @return AnnotationsInterface[]
     */
    private function filterReflectionsByAnnotation(array $reflections, string $annotation): array
    {
        return array_filter($reflections, function (AnnotationsInterface $reflection) use ($annotation) {
            return $reflection->hasAnnotation($annotation);
        });
    }
}
