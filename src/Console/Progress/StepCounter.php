<?php declare(strict_types=1);

namespace ApiGen\Console\Progress;

use ApiGen\Element\Namespace_\ParentEmptyNamespacesResolver;
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

    /**
     * @var ParentEmptyNamespacesResolver
     */
    private $parentEmptyNamespacesResolver;

    public function __construct(
        ReflectionStorage $reflectionStorage,
        NamespaceReflectionCollector $namespaceReflectionCollector,
        ParentEmptyNamespacesResolver $parentEmptyNamespacesResolver
    ) {
        $this->reflectionStorage = $reflectionStorage;
        $this->namespaceReflectionCollector = $namespaceReflectionCollector;
        $this->parentEmptyNamespacesResolver = $parentEmptyNamespacesResolver;
    }

    public function getStepCount(): int
    {
        $parentEmptyNamespaces = $this->parentEmptyNamespacesResolver->resolve(
            $this->namespaceReflectionCollector->getNamespaces()
        );

        return $this->getSourceCodeStepCount()
            + count($parentEmptyNamespaces)
            + count($this->namespaceReflectionCollector->getNamespaces())
            + count($this->reflectionStorage->getClassReflections())
            + count($this->reflectionStorage->getExceptionReflections())
            + count($this->reflectionStorage->getTraitReflections())
            + count($this->reflectionStorage->getInterfaceReflections())
            + count($this->reflectionStorage->getFunctionReflections())
            + $this->getOverviewPagesCount();
    }

    private function getSourceCodeStepCount(): int
    {
        $count = 0;
        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            if ($classReflection->getFileName()) {
                $count++;
            }
        }

        foreach ($this->reflectionStorage->getExceptionReflections() as $exceptionReflection) {
            if ($exceptionReflection->getFileName()) {
                $count++;
            }
        }

        foreach ($this->reflectionStorage->getInterfaceReflections() as $interfaceReflection) {
            if ($interfaceReflection->getFileName()) {
                $count++;
            }
        }

        foreach ($this->reflectionStorage->getTraitReflections() as $traitReflection) {
            if ($traitReflection->getFileName()) {
                $count++;
            }
        }

        foreach ($this->reflectionStorage->getFunctionReflections() as $functionReflection) {
            if ($functionReflection->getFileName()) {
                $count++;
            }
        }

        return $count;
    }

    private function getOverviewPagesCount(): int
    {
        $count = 2; // index.html + elementlist.js
        if (count($this->reflectionStorage->getClassReflections())) {
            $count++; // classes.html
        }

        if (count($this->reflectionStorage->getExceptionReflections())) {
            $count++; // exceptions.html
        }

        if (count($this->reflectionStorage->getInterfaceReflections())) {
            $count++; // interfaces.html
        }

        if (count($this->reflectionStorage->getTraitReflections())) {
            $count++; // traits.html
        }

        if (count($this->reflectionStorage->getFunctionReflections())) {
            $count++; // functions.html
        }

        return $count;
    }
}
