<?php declare(strict_types=1);

namespace ApiGen\Progress;

use ApiGen\Element\Namespaces\NamespaceStorage;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;

final class StepCounter
{
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    /**
     * @var NamespaceStorage
     */
    private $namespaceStorage;

    public function __construct(ReflectionStorageInterface $reflectionStorage, NamespaceStorage $namespaceStorage)
    {
        $this->reflectionStorage = $reflectionStorage;
        $this->namespaceStorage = $namespaceStorage;
    }

    public function getStepCount(): int
    {
        return $this->getSourceCodeStepCount()
            + count($this->namespaceStorage->getNamespaces())
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
