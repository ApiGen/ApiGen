<?php declare(strict_types=1);

namespace ApiGen\Element\ReflectionCollector;

use ApiGen\Configuration\Configuration;
use ApiGen\Element\Contract\ReflectionCollector\BasicReflectionCollectorInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Helper\ReflectionAnalyzer;

final class NamespaceReflectionCollector implements BasicReflectionCollectorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var string
     */
    private $activeNamespace;

    /**
     * @var
     */
    private $collectedReflections;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function setActiveNamespace(string $activeNamespace): void
    {
        $this->activeNamespace = $activeNamespace;
    }

    public function processReflection(AbstractReflectionInterface $reflection): void
    {
        // namespaced reflection

        dump($reflection);
        die;

        foreach ($this->configuration->getAnnotationGroups() as $annotation) {

            $reflectionInterface = ReflectionAnalyzer::getReflectionInterfaceFromReflection($reflection);
            $this->collectedReflections[$reflectionInterface][$namespace][$reflection->getName()] = $reflection;
        }
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassReflections(): array
    {
        return $this->collectedReflections[ClassReflectionInterface::class][$this->activeNamespace] ?? [];
    }

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaceReflections(): array
    {
        return $this->collectedReflections[InterfaceReflectionInterface::class][$this->activeNamespace] ?? [];
    }

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraitReflections(): array
    {
        return $this->collectedReflections[TraitReflectionInterface::class][$this->activeNamespace] ?? [];
    }

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctionReflections(): array
    {
        return $this->collectedReflections[FunctionReflectionInterface::class][$this->activeNamespace] ?? [];
    }

    public function hasAnyElements(): bool
    {
        // TODO: Implement hasAnyElements() method.
    }
}
