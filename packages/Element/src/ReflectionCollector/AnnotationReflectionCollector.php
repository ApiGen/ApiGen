<?php declare(strict_types=1);

namespace ApiGen\Element\ReflectionCollector;

use ApiGen\Configuration\Configuration;
use ApiGen\Element\Contract\ReflectionCollector\AdvancedReflectionCollectorInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Helper\ReflectionAnalyzer;

final class AnnotationReflectionCollector implements AdvancedReflectionCollectorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var mixed[]
     */
    private $collectedReflections = [];

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function processReflection(AbstractReflectionInterface $reflection): void
    {
        if (! $reflection instanceof AnnotationsInterface) {
            return;
        }

        foreach ($this->configuration->getAnnotationGroups() as $annotation) {
            if (! $reflection->hasAnnotation($annotation)) {
                continue;
            }

            $reflectionInterface = ReflectionAnalyzer::getReflectionInterfaceFromReflection($reflection);
            $this->collectedReflections[$reflectionInterface][$annotation][$reflection->getName()] = $reflection;
        }
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassReflections(string $activeAnnotation): array
    {
        return $this->collectedReflections[ClassReflectionInterface::class][$activeAnnotation] ?? [];
    }

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaceReflections(string $activeAnnotation): array
    {
        return $this->collectedReflections[InterfaceReflectionInterface::class][$activeAnnotation] ?? [];
    }

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraitReflections(string $activeAnnotation): array
    {
        return $this->collectedReflections[TraitReflectionInterface::class][$activeAnnotation] ?? [];
    }

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctionReflections(string $activeAnnotation): array
    {
        return $this->collectedReflections[FunctionReflectionInterface::class][$activeAnnotation] ?? [];
    }

    /**
     * @return ClassMethodReflectionInterface[]|TraitMethodReflectionInterface[]
     */
    public function getClassOrTraitMethodReflections(string $activeAnnotation): array
    {
        return ($this->collectedReflections[ClassMethodReflectionInterface::class][$activeAnnotation] ?? [])
            + ($this->collectedReflections[TraitMethodReflectionInterface::class][$activeAnnotation] ?? []);
    }

    /**
     * @return ClassPropertyReflectionInterface[]|TraitPropertyReflectionInterface[]
     */
    public function getClassOrTraitPropertyReflections(string $activeAnnotation): array
    {
        return ($this->collectedReflections[ClassPropertyReflectionInterface::class][$activeAnnotation] ?? [])
            + ($this->collectedReflections[TraitPropertyReflectionInterface::class][$activeAnnotation] ?? []);
    }

    /**
     * @return ClassConstantReflectionInterface[]|InterfaceConstantReflectionInterface[]
     */
    public function getClassOrInterfaceConstantReflections(string $activeAnnotation): array
    {
        return ($this->collectedReflections[ClassConstantReflectionInterface::class][$activeAnnotation] ?? [])
            + ($this->collectedReflections[InterfaceConstantReflectionInterface::class][$activeAnnotation] ?? []);
    }

    public function hasAnyElements(): bool
    {
        return (bool) count($this->collectedReflections);
    }
}
