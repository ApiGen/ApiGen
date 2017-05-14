<?php declare(strict_types=1);

namespace ApiGen\Element\Cache;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;

/**
 * Invoke all old reflection conversions, so they can be collected.
 */
final class ReflectionWarmUpper
{
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    public function __construct(ReflectionStorageInterface $reflectionStorage)
    {
        $this->reflectionStorage = $reflectionStorage;
    }

    public function warmUp(): void
    {
        $this->warmUpClassReflections();
        $this->warmUpInterfaceReflections();
        $this->warmUpTraitReflections();
        $this->warmUpFunctionReflections();
    }

    private function warmUpClassReflections(): void
    {
        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            $classReflection->getOwnConstants();
            $classReflection->getOwnProperties();
            foreach ($classReflection->getOwnMethods() as $methodReflection) {
                $methodReflection->getParameters();
            }
        }
    }

    private function warmUpInterfaceReflections(): void
    {
        foreach ($this->reflectionStorage->getInterfaceReflections() as $interfaceReflection) {
            $interfaceReflection->getOwnConstants();
            foreach ($interfaceReflection->getOwnMethods() as $methodReflection) {
                 $methodReflection->getParameters();
            }
        }
    }

    private function warmUpTraitReflections(): void
    {
        foreach ($this->reflectionStorage->getTraitReflections() as $traitReflection) {
            $traitReflection->getOwnProperties();
            foreach ($traitReflection->getOwnMethods() as $methodReflection) {
                $methodReflection->getParameters();
            }
        }
    }

    private function warmUpFunctionReflections(): void
    {
        foreach ($this->reflectionStorage->getFunctionReflections() as $functionReflection) {
            $functionReflection->getParameters();
        }
    }
}
