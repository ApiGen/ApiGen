<?php declare(strict_types=1);

namespace ApiGen\Element\ReflectionCollector;

use ApiGen\Contract\Configuration\ConfigurationInterface;
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

final class AnnotationReflectionCollector extends AbstractReflectionCollector
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var string
     */
    private $activeAnnotation;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function setActiveAnnotation(string $activeAnnotation): void
    {
        $this->activeAnnotation = $activeAnnotation;
    }

    public function processReflection(AbstractReflectionInterface $reflection): void
    {
        if (! $reflection instanceof AnnotationsInterface) {
            return;
        }

        foreach ($this->configuration->getAnnotationGroups() as $annotation) {
            // @todo allow value as well?
            // $reflection->hasAnnotation($annotation, $value);
            // $reflection->hasAnnotation('@author', 'Tomas Votruba');
            // url: annotation-author-tomasvotruba.html

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
    public function getClassReflections(): array
    {
        return $this->collectedReflections[ClassReflectionInterface::class][$this->activeAnnotation] ?? [];
    }

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaceReflections(): array
    {
        return $this->collectedReflections[InterfaceReflectionInterface::class][$this->activeAnnotation] ?? [];
    }

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraitReflections(): array
    {
        return $this->collectedReflections[TraitReflectionInterface::class][$this->activeAnnotation] ?? [];
    }

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctionReflections(): array
    {
        return $this->collectedReflections[FunctionReflectionInterface::class][$this->activeAnnotation] ?? [];
    }

    /**
     * @return ClassMethodReflectionInterface[]|TraitMethodReflectionInterface[]
     */
    public function getClassOrTraitMethodReflections(): array
    {
        return ($this->collectedReflections[ClassMethodReflectionInterface::class][$this->activeAnnotation] ?? [])
            + ($this->collectedReflections[TraitMethodReflectionInterface::class][$this->activeAnnotation] ?? []);
    }

    /**
     * @return ClassPropertyReflectionInterface[]|TraitPropertyReflectionInterface[]
     */
    public function getClassOrTraitPropertyReflections(): array
    {
        return ($this->collectedReflections[ClassPropertyReflectionInterface::class][$this->activeAnnotation] ?? [])
            + ($this->collectedReflections[TraitPropertyReflectionInterface::class][$this->activeAnnotation] ?? []);
    }

    /**
     * @return ClassConstantReflectionInterface[]|InterfaceConstantReflectionInterface[]
     */
    public function getClassOrInterfaceConstantReflections(): array
    {
        return ($this->collectedReflections[ClassConstantReflectionInterface::class][$this->activeAnnotation] ?? [])
            + ($this->collectedReflections[InterfaceConstantReflectionInterface::class][$this->activeAnnotation] ?? []);
    }
}
