<?php declare(strict_types=1);

namespace ApiGen\Reflection\Parser;

use ApiGen\BetterReflection\Reflector\ClassReflectorFactory;
use ApiGen\BetterReflection\Reflector\FunctionReflectorFactory;
use ApiGen\Element\Cache\ReflectionWarmUpper;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\Reflection\TransformerCollector;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\FunctionReflector;

final class Parser
{
    /**
     * @var TransformerCollector
     */
    private $transformerCollector;

    /**
     * @var ReflectionStorage
     */
    private $reflectionStorage;

    /**
     * @var ReflectionWarmUpper
     */
    private $reflectionWarmUpper;

    /**
     * @var FunctionReflector
     */
    private $functionReflector;

    /**
     * @var ClassReflector
     */
    private $classReflector;

    /**
     * @var FunctionReflectorFactory
     */
    private $functionReflectorFactory;

    /**
     * @var ClassReflectorFactory
     */
    private $classReflectorFactory;

    public function __construct(
        TransformerCollector $transformerCollector,
        ReflectionStorage $reflectionStorage,
        ReflectionWarmUpper $reflectionWarmUpper,
        FunctionReflectorFactory $functionReflectorFactory,
        ClassReflectorFactory $classReflectorFactory
    ) {
        $this->transformerCollector = $transformerCollector;
        $this->reflectionStorage = $reflectionStorage;
        $this->reflectionWarmUpper = $reflectionWarmUpper;
        $this->functionReflectorFactory = $functionReflectorFactory;
        $this->classReflectorFactory = $classReflectorFactory;
    }

    public function parse(): void
    {
        $this->parseClassElements();
        $this->parseFunctions();

        $this->reflectionWarmUpper->warmUp();
    }

    /**
     * @param object[] $classInterfaceAndTraitReflections
     */
    private function separateClassInterfaceAndTraitReflections(array $classInterfaceAndTraitReflections): void
    {
        $classReflections = array_filter($classInterfaceAndTraitReflections, function ($reflection) {
            return $reflection instanceof ClassReflectionInterface;
        });
        $this->reflectionStorage->addClassReflections($classReflections);

        $interfaceReflections = array_filter($classInterfaceAndTraitReflections, function ($reflection) {
            return $reflection instanceof InterfaceReflectionInterface;
        });
        $this->reflectionStorage->addInterfaceReflections($interfaceReflections);

        $traitReflections = array_filter($classInterfaceAndTraitReflections, function ($reflection) {
            return $reflection instanceof TraitReflectionInterface;
        });
        $this->reflectionStorage->addTraitReflections($traitReflections);
    }

    /**
     * @return FunctionReflectionInterface[]
     */
    private function transformBetterFunctionReflections(): array
    {
        $betterFunctionReflections = $this->getFunctionReflector()->getAllFunctions();

        return $this->transformerCollector->transformGroup($betterFunctionReflections);
    }

    /**
     * @return ClassReflectionInterface[]
     */
    private function transformBetterClassInterfaceAndTraitReflections(): array
    {
        $betterClassReflections = $this->getClassReflector()->getAllClasses();
        $allReflections = $this->resolveParentClassesInterfacesAndTraits($betterClassReflections);

        return $this->transformerCollector->transformGroup($allReflections);
    }

    /**
     * @param ReflectionClass[] $betterClassReflections
     * @return ReflectionClass[]
     */
    private function resolveParentClassesInterfacesAndTraits(array $betterClassReflections): array
    {
        $reflections = [];

        foreach ($betterClassReflections as $reflection) {
            $reflections[$reflection->getName()] = $reflection;
        }

        $reflections = $this->resolveParentClasses($reflections);
        $reflections = $this->resolveParentInterfaces($reflections);
        $reflections = $this->resolveParentTraits($reflections);

        return $reflections;
    }

    /**
     * @param ReflectionClass[] $betterClassReflections
     * @return ReflectionClass[]
     */
    private function resolveParentClasses(array $betterClassReflections): array
    {
        foreach ($betterClassReflections as $reflection) {
            $class = $reflection;
            while ($parentClass = $class->getParentClass()) {
                $class = $parentClass;

                /** @var ClassReflectionInterface $parentClass */
                if (isset($betterClassReflections[$parentClass->getName()])) {
                    continue;
                }

                $betterClassReflections[$parentClass->getName()] = $parentClass;
            }
        }

        return $betterClassReflections;
    }

    /**
     * @param ReflectionClass[] $betterClassReflections
     * @return ReflectionClass[]
     */
    private function resolveParentInterfaces(array $betterClassReflections): array
    {
        foreach ($betterClassReflections as $betterClassReflection) {
            foreach ($betterClassReflection->getInterfaces() as $interface) {
                if (isset($betterClassReflections[$interface->getName()])) {
                    continue;
                }

                $betterClassReflections[$interface->getName()] = $interface;
            }
        }

        return $betterClassReflections;
    }

    /**
     * @param ReflectionClass[] $betterClassReflections
     * @return ReflectionClass[]
     */
    private function resolveParentTraits(array $betterClassReflections): array
    {
        foreach ($betterClassReflections as $betterClassReflection) {
            foreach ($betterClassReflection->getTraits() as $trait) {
                if (isset($betterClassReflections[$trait->getName()])) {
                    continue;
                }

                $betterClassReflections[$trait->getName()] = $trait;
            }
        }

        return $betterClassReflections;
    }

    private function parseClassElements(): void
    {
        $classInterfaceAndTraitReflections = $this->transformBetterClassInterfaceAndTraitReflections();
        $this->separateClassInterfaceAndTraitReflections($classInterfaceAndTraitReflections);
    }

    private function parseFunctions(): void
    {
        $functionReflections = $this->transformBetterFunctionReflections();
        $this->reflectionStorage->setFunctionReflections($functionReflections);
    }

    private function getClassReflector(): ClassReflector
    {
        return $this->classReflector ?? $this->classReflector = $this->classReflectorFactory->create();
    }

    private function getFunctionReflector(): FunctionReflector
    {
        return $this->functionReflector ?? $this->functionReflector = $this->functionReflectorFactory->create();
    }
}
