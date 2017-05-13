<?php declare(strict_types=1);

namespace ApiGen\Element\ReflectionCollector;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Element\Contract\ReflectionCollector\ReflectionCollectorInterface;
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

// todo: add abstract collector with getXmethods?
final class AnnotationReflectionCollector implements ReflectionCollectorInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var mixed
     */
    private $collectedReflections = [];

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param object $reflection
     */
    public function processReflection($reflection): void
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

            $this->collectedReflections[get_class($reflection)][$annotation][$reflection->getName()] = $reflection;
        }
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassReflections(): array
    {
        return $this->collectedReflections[ClassReflectionInterface::class] ?? [];
    }

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaceReflections(): array
    {
        return $this->collectedReflections[InterfaceReflectionInterface::class] ?? [];
    }

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraitReflections(): array
    {
        return $this->collectedReflections[TraitReflectionInterface::class] ?? [];
    }

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctionReflections(): array
    {
        return $this->collectedReflections[FunctionReflectionInterface::class] ?? [];
    }

    /**
     * @return ClassMethodReflectionInterface[]|TraitMethodReflectionInterface[]
     */
    public function getClassOrTraitMethodReflections(): array
    {
        return $this->collectedReflections[ClassMethodReflectionInterface::class] ?? []
            + $this->collectedReflections[TraitMethodReflectionInterface::class] ?? [];
    }

    /**
     * @return ClassPropertyReflectionInterface[]|TraitPropertyReflectionInterface[]
     */
    public function getClassOrTraitPropertyReflections(): array
    {
        return $this->collectedReflections[ClassPropertyReflectionInterface::class] ?? []
            + $this->collectedReflections[TraitPropertyReflectionInterface::class] ?? [];
    }

    /**
     * @return ClassConstantReflectionInterface[]|InterfaceConstantReflectionInterface[]
     */
    public function getClassOrInterfaceConstantReflections(): array
    {
        return $this->collectedReflections[ClassConstantReflectionInterface::class] ?? []
            + $this->collectedReflections[InterfaceConstantReflectionInterface::class] ?? [];
    }

    public function hasAnyElements(): bool
    {
        return (bool) count($this->collectedReflections);
    }
}
