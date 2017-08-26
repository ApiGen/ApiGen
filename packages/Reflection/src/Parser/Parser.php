<?php declare(strict_types=1);

namespace ApiGen\Reflection\Parser;

use ApiGen\BetterReflection\SourceLocator\DirectorySourceLocatorFactory;
use ApiGen\BetterReflection\SourceLocator\FileSourceLocatorFactory;
use ApiGen\Element\Cache\ReflectionWarmUpper;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\Reflection\TransformerCollector;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Type\SourceLocator;

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
     * @var DirectorySourceLocatorFactory
     */
    private $directorySourceLocatorFactory;
    /**
     * @var FileSourceLocatorFactory
     */
    private $fileSourceLocatorFactory;

    public function __construct(
        TransformerCollector $transformerCollector,
        ReflectionStorage $reflectionStorage,
        ReflectionWarmUpper $reflectionWarmUpper,
        DirectorySourceLocatorFactory $directorySourceLocatorFactory,
        FileSourceLocatorFactory $fileSourceLocatorFactory
    ) {
        $this->transformerCollector = $transformerCollector;
        $this->reflectionStorage = $reflectionStorage;
        $this->reflectionWarmUpper = $reflectionWarmUpper;
        $this->directorySourceLocatorFactory = $directorySourceLocatorFactory;
        $this->fileSourceLocatorFactory = $fileSourceLocatorFactory;
    }

    /**
     * @param string[] $sources
     */
    public function parseFilesAndDirectories(array $sources): void
    {
        $files = [];
        $directories = [];
        foreach ($sources as $source) {
            if (is_dir($source)) {
                $directories[] = $source;
            } else {
                $files[] = $source;
            }
        }

        $this->parseDirectories($directories);
        $this->parseFiles($files);
    }

    /**
     * @param string[] $directories
     */
    private function parseDirectories(array $directories): void
    {
        $directoriesSourceLocator = $this->directorySourceLocatorFactory->createFromDirectories($directories);

        $this->parseClassElements($directoriesSourceLocator);
        $this->parseFunctions($directoriesSourceLocator);

        $this->reflectionWarmUpper->warmUp();
    }

    /**
     * @param string[] $files
     */
    private function parseFiles(array $files): void
    {
        $filesSourceLocator = $this->fileSourceLocatorFactory->createFromFiles($files);

        $this->parseClassElements($filesSourceLocator);
        $this->parseFunctions($filesSourceLocator);

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
    private function transformBetterFunctionReflections(FunctionReflector $functionReflector): array
    {
        $betterFunctionReflections = $functionReflector->getAllFunctions();

        return $this->transformerCollector->transformGroup($betterFunctionReflections);
    }

    /**
     * @return ClassReflectionInterface[]
     */
    private function transformBetterClassInterfaceAndTraitReflections(ClassReflector $classReflector): array
    {
        $betterClassReflections = $classReflector->getAllClasses();
        $allReflections = $this->resolveParentClassesInterfacesAndTraits($betterClassReflections);

        return $this->transformerCollector->transformGroup($allReflections);
    }

    /**
     * @param ClassReflectionInterface[]
     * @return ClassReflectionInterface[]
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
     * @param ClassReflectionInterface[]
     * @return ClassReflectionInterface[]
     */
    private function resolveParentClasses(array $reflections): array
    {
        foreach ($reflections as $reflection) {
            $class = $reflection;
            while ($parentClass = $class->getParentClass()) {
                if (! isset($reflections[$parentClass->getName()])) {
                    $reflections[$parentClass->getName()] = $parentClass;
                }

                $class = $parentClass;
            }
        }

        return $reflections;
    }

    /**
     * @param ClassReflectionInterface[]
     * @return ClassReflectionInterface[]
     */
    private function resolveParentInterfaces(array $reflections): array
    {
        foreach ($reflections as $reflection) {
            foreach ($reflection->getInterfaces() as $interface) {
                if (! isset($reflections[$interface->getName()])) {
                    $reflections[$interface->getName()] = $interface;
                }
            }
        }

        return $reflections;
    }

    /**
     * @param ClassReflectionInterface[]
     * @return ClassReflectionInterface[]
     */
    private function resolveParentTraits(array $reflections): array
    {
        foreach ($reflections as $reflection) {
            foreach ($reflection->getTraits() as $trait) {
                if (! isset($reflections[$trait->getName()])) {
                    $reflections[$trait->getName()] = $trait;
                }
            }
        }

        return $reflections;
    }

    private function parseClassElements(SourceLocator $sourceLocator): void
    {
        $classReflector = new ClassReflector($sourceLocator);
        $classInterfaceAndTraitReflections = $this->transformBetterClassInterfaceAndTraitReflections($classReflector);
        $this->separateClassInterfaceAndTraitReflections($classInterfaceAndTraitReflections);
    }

    private function parseFunctions(SourceLocator $sourceLocator): void
    {
        $functionReflector = new FunctionReflector($sourceLocator);
        $functionReflections = $this->transformBetterFunctionReflections($functionReflector);
        $this->reflectionStorage->setFunctionReflections($functionReflections);
    }
}
