<?php declare(strict_types=1);

namespace ApiGen\Reflection\Parser;

use ApiGen\Element\Cache\ReflectionWarmUpper;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\Reflection\TransformerCollector;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\AutoloadSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\ComposerSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;
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

    public function __construct(
        TransformerCollector $transformerCollector,
        ReflectionStorage $reflectionStorage,
        ReflectionWarmUpper $reflectionWarmUpper
    ) {
        $this->transformerCollector = $transformerCollector;
        $this->reflectionStorage = $reflectionStorage;
        $this->reflectionWarmUpper = $reflectionWarmUpper;
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
        $directoriesSourceLocator = $this->createDirectoriesSource($directories);

        $this->parseClassElements($directoriesSourceLocator);
        $this->parseFunctions($directoriesSourceLocator);

        $this->reflectionWarmUpper->warmUp();
    }

    /**
     * @param string[] $files
     */
    private function parseFiles(array $files): void
    {
        $filesSourceLocator = $this->createFilesSource($files);

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

        return $this->transformerCollector->transformGroup($betterClassReflections);
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

    /**
     * @param string[] $directories
     */
    private function createDirectoriesSource(array $directories): SourceLocator
    {
        $locators = [
            new DirectoriesSourceLocator($directories),
            new AutoloadSourceLocator(),
            new PhpInternalSourceLocator()
        ];

        foreach ($directories as $directory) {
            $autoload = dirname($directory) . '/vendor/autoload.php';
            if (is_file($autoload)) {
                $locators[] = new ComposerSourceLocator(include $autoload);
            }
        }

        return new AggregateSourceLocator($locators);
    }

    /**
     * @param string[] $files
     */
    private function createFilesSource(array $files): SourceLocator
    {
        $locators = [
            new AutoloadSourceLocator(),
            new PhpInternalSourceLocator()
        ];

        foreach ($files as $file) {
            $locators[] = new SingleFileSourceLocator($file);
        }

        return new AggregateSourceLocator($locators);
    }
}
