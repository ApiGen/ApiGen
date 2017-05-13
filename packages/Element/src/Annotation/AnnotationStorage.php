<?php declare(strict_types=1);

namespace ApiGen\Element\Annotation;

use ApiGen\Element\ReflectionCollector\AnnotationReflectionCollector;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;

final class AnnotationStorage
{
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    /**
     * @var AnnotationReflectionCollector
     */
    private $annotationReflectionCollector;

    public function __construct(ReflectionStorageInterface $reflectionStorage, AnnotationReflectionCollector $annotationReflectionCollector)
    {
        $this->reflectionStorage = $reflectionStorage;
        $this->annotationReflectionCollector = $annotationReflectionCollector;
    }

    public function findByAnnotation(string $annotation): SingleAnnotationStorage
    {
//        $functionReflections = $this->filterReflectionsByAnnotation(
//            $this->reflectionStorage->getFunctionReflections(),
//            $annotation
//        );
//
//        $classReflections = $this->filterReflectionsByAnnotation(
//            $this->reflectionStorage->getClassReflections(),
//            $annotation
//        );
//
//        $interfaceReflections = $this->filterReflectionsByAnnotation(
//            $this->reflectionStorage->getInterfaceReflections(),
//            $annotation
//        );
//
//        $traitReflections = $this->filterReflectionsByAnnotation(
//            $this->reflectionStorage->getTraitReflections(),
//            $annotation
//        );
//
//        $constantReflections = [];
//        $methodReflections = [];
//        $propertyReflections = [];
//        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
//            $methodReflections = $this->extractByAnnotationAndMerge(
//                $classReflection->getOwnMethods(),
//                $annotation,
//                $methodReflections
//            );
//
//            $constantReflections = $this->extractByAnnotationAndMerge(
//                $classReflection->getOwnConstants(),
//                $annotation,
//                $constantReflections
//            );
//
//            $propertyReflections = $this->extractByAnnotationAndMerge(
//                $classReflection->getOwnProperties(),
//                $annotation,
//                $propertyReflections
//            );
//        }

//        return new SingleAnnotationStorage(
//            $annotation,
//            $classReflections,
//            $interfaceReflections,
//            $traitReflections,
//            $functionReflections,
//            $methodReflections,
//            $propertyReflections,
//            $constantReflections
//        );

        $this->annotationReflectionCollector->setActiveAnnotation($annotation);

        return new SingleAnnotationStorage(
            $annotation,
            $this->annotationReflectionCollector->getClassReflections(),
            $this->annotationReflectionCollector->getInterfaceReflections(),
            $this->annotationReflectionCollector->getTraitReflections(),
            $this->annotationReflectionCollector->getFunctionReflections(),
            $this->annotationReflectionCollector->getClassOrTraitMethodReflections(),
            $this->annotationReflectionCollector->getClassOrTraitPropertyReflections(),
            $this->annotationReflectionCollector->getClassOrInterfaceConstantReflections()
        );
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
