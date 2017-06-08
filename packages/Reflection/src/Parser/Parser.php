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
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
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
        // @see \ApiGen\Parser\Broker\Backend: https://github.com/ApiGen/ApiGen/blob/fa603928b656a9e7c826e001f5295200d23f9712/src/Parser/Broker/Backend.php#L174

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
        // @todo: use FileIteratorSourceLocator and FinderInterface
        // such service scan be replaced in config by own with custom finder implementation
        return new AggregateSourceLocator([
            new DirectoriesSourceLocator($directories),
            new AutoloadSourceLocator(),
            new PhpInternalSourceLocator()
        ]);
    }
}
