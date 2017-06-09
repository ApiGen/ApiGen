<?php declare(strict_types=1);

namespace ApiGen\Console\Progress;

use ApiGen\Element\ReflectionCollector\NamespaceReflectionCollector;
use ApiGen\Reflection\ReflectionStorage;

final class StepCounter
{
    /**
     * @var ReflectionStorage
     */
    private $reflectionStorage;

    /**
     * @var NamespaceReflectionCollector
     */
    private $namespaceReflectionCollector;

    public function __construct(
        ReflectionStorage $reflectionStorage,
        NamespaceReflectionCollector $namespaceReflectionCollector
    ) {
        $this->reflectionStorage = $reflectionStorage;
        $this->namespaceReflectionCollector = $namespaceReflectionCollector;
    }

    public function getStepCount(): int
    {
        return $this->getSourceCodeStepCount()
            + count($this->namespaceReflectionCollector->getNamespaces())
            + count($this->reflectionStorage->getClassReflections())
            + count($this->reflectionStorage->getTraitReflections())
            + count($this->reflectionStorage->getInterfaceReflections())
            + count($this->reflectionStorage->getFunctionReflections());
    }

    private function getSourceCodeStepCount(): int
    {
        return count($this->reflectionStorage->getClassReflections())
            + count($this->reflectionStorage->getInterfaceReflections())
            + count($this->reflectionStorage->getTraitReflections())
            + count($this->reflectionStorage->getFunctionReflections());
    }
}
