<?php declare(strict_types=1);

namespace ApiGen\Reflection\Parser;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\InterfaceReflectionInterface;
use ApiGen\Reflection\Reflection\InterfaceReflection;
use ApiGen\Reflection\Reflection\TraitReflection;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use Roave\BetterReflection\Reflection\ReflectionClass;
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
     * @var ClassReflectionInterface[]
     */
    private $classReflections = [];

    /**
     * @var InterfaceReflection[]
     */
    private $interfaceReflections = [];

    /**
     * @var TraitReflection[]
     */
    private $traitReflections = [];

    /**
     * @var FunctionReflectionInterface[]
     */
    private $functionReflections = [];

    public function __construct(TransformerCollectorInterface $transformerCollector)
    {
        $this->transformerCollector = $transformerCollector;
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
     * @return ClassReflectionInterface[]
     */
    public function getClassReflections(): array
    {
        return $this->classReflections;
    }

    /**
     * @return InterfaceReflection[]
     */
    public function getInterfaceReflections(): array
    {
        return $this->interfaceReflections;
    }

    /**
     * @return TraitReflection[]
     */
    public function getTraitReflections(): array
    {
        return $this->traitReflections;
    }

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctionReflections(): array
    {
        return $this->functionReflections;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    private function transformBetterClassInterfaceAndTraitReflections(ClassReflector $classReflector): array
    {
        $betterClassReflections = $classReflector->getAllClasses();

        $this->transformerCollector->transformGroup($betterClassReflections);
    }

    /**
     * @param object[] $classInterfaceAndTraitReflections
     */
    private function separateClassInterfaceAndTraitReflections(array $classInterfaceAndTraitReflections): void
    {
        $this->classReflections = array_filter($classInterfaceAndTraitReflections, function ($reflection) {
            return $reflection instanceof ClassReflectionInterface;
        });

        $this->interfaceReflections = array_filter($classInterfaceAndTraitReflections, function ($reflection) {
            return $reflection instanceof InterfaceReflection;
        });

        $this->traitReflections = array_filter($classInterfaceAndTraitReflections, function ($reflection) {
            return $reflection instanceof TraitReflection;
        });
    }

    /**
     * @return FunctionReflectionInterface[]
     */
    private function transformBetterFunctionReflections(FunctionReflector $functionReflector): array
    {
        $betterFunctionReflections = $functionReflector->getAllFunctions();

        return $this->transformerCollector->transformGroup($betterFunctionReflections);
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
        $this->functionReflections = $this->transformBetterFunctionReflections($functionReflector);
    }

    /**
     * @param string[] $directories
     */
    private function createDirectoriesSource(array $directories): DirectoriesSourceLocator
    {
        return new DirectoriesSourceLocator($directories);
    }

    // @legacy bellow @see \ApiGen\Parser\ParserStorage

    /**
     * @return ClassReflectionInterface[]|InterfaceReflectionInterface[]
     */
    public function getDirectImplementersOfInterface(InterfaceReflectionInterface $interfaceReflection): array
    {
        $implementers = [];
        foreach ($this->getClassReflections() as $class) {
            if ($this->isAllowedDirectImplementer($class, $interfaceReflection->getName())) {
                $implementers[] = $class;
            }
        }

        uksort($implementers, 'strcasecmp');

        return $implementers;
    }

    /**
     * @return ClassReflectionInterface[]|InterfaceReflectionInterface[]
     */
    public function getIndirectImplementersOfInterface(InterfaceReflectionInterface $interfaceReflection): array
    {
        $implementers = [];
        foreach ($this->getClassReflections() as $class) {
            if ($this->isAllowedIndirectImplementer($class, $interfaceReflection->getName())) {
                $implementers[] = $class;
            }
        }

        uksort($implementers, 'strcasecmp');
        return $implementers;
    }

    private function isAllowedDirectImplementer(ClassReflectionInterface $class, string $name): bool
    {
        return $class->isDocumented() && in_array($name, $class->getOwnInterfaceNames());
    }

    private function isAllowedIndirectImplementer(ClassReflectionInterface $class, string $name): bool
    {
        return $class->isDocumented() && $class->implementsInterface($name)
            && ! in_array($name, $class->getOwnInterfaceNames());
    }
}
