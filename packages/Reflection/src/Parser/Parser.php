<?php declare(strict_types=1);

namespace ApiGen\Reflection\Parser;

use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;

final class Parser implements ParserInterface
{
    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    public function __construct(TransformerCollectorInterface $transformerCollector, ReflectionStorageInterface $reflectionStorage)
    {
        $this->transformerCollector = $transformerCollector;
        $this->reflectionStorage = $reflectionStorage;
    }

    /**
     * @param string[] $directories
     */
    public function parseDirectories(array $directories): void
    {
        // @legacy allowed to specify extensions and exclude, removed for now
        $directoriesSourceLocator = $this->createDirectoriesSource($directories);

        $this->parseClassElements($directoriesSourceLocator);
        $this->parseFunctions($directoriesSourceLocator);

        // @legacy
        // Add classes from @param, @var, @return, @throws annotations as well
        // as parent classes to the overall class list.
        // @see \ApiGen\Parser\Broker\Backend
    }

    /**
     * @param object[] $classInterfaceAndTraitReflections
     */
    private function separateClassInterfaceAndTraitReflections(array $classInterfaceAndTraitReflections): void
    {
        $classReflections = array_filter($classInterfaceAndTraitReflections, function ($reflection) {
            return $reflection instanceof ClassReflectionInterface;
        });
        sort($classReflections);
        $this->reflectionStorage->setClassReflections($classReflections);

        $interfaceReflections = array_filter($classInterfaceAndTraitReflections, function ($reflection) {
            return $reflection instanceof InterfaceReflectionInterface;
        });
        sort($interfaceReflections);
        $this->reflectionStorage->setInterfaceReflections($interfaceReflections);

        $traitReflections = array_filter($classInterfaceAndTraitReflections, function ($reflection) {
            return $reflection instanceof TraitReflectionInterface;
        });
        sort($traitReflections);
        $this->reflectionStorage->setTraitReflections($traitReflections);
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

    private function parseClassElements(DirectoriesSourceLocator $directoriesSourceLocator): void
    {
        $classReflector = new ClassReflector($directoriesSourceLocator);
        $classInterfaceAndTraitReflections = $this->transformBetterClassInterfaceAndTraitReflections($classReflector);
        $this->separateClassInterfaceAndTraitReflections($classInterfaceAndTraitReflections);
    }

    private function parseFunctions(DirectoriesSourceLocator $directoriesSourceLocator): void
    {
        $functionReflector = new FunctionReflector($directoriesSourceLocator);
        $functionReflections = $this->transformBetterFunctionReflections($functionReflector);
        $this->reflectionStorage->setFunctionReflections($functionReflections);
    }

    /**
     * @param string[] $directories
     */
    private function createDirectoriesSource(array $directories): DirectoriesSourceLocator
    {
        // @todo: use FileIteratorSourceLocator and FinderInterface
        // such service scan be replaced in config by own with custom finder implementation
        return new DirectoriesSourceLocator($directories);
    }
}
