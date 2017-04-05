<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Parser;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\ElementReflection\Reflection\InterfaceReflection;
use ApiGen\ElementReflection\Reflection\TraitReflection;
use ApiGen\ReflectionToElementTransformer\Contract\TransformerCollectorInterface;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;

final class Parser
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
        $directoriesSourceLocator = new DirectoriesSourceLocator($directories);

        $classReflector = new ClassReflector($directoriesSourceLocator);
        $classInterfaceAndTraitReflections = $this->transformBetterClassInterfaceAndTraitReflections($classReflector);
        $this->separateClassInterfaceAndTraitReflections($classInterfaceAndTraitReflections);

        $functionReflector = new FunctionReflector($directoriesSourceLocator);
        $this->functionReflections = $this->transformBetterFunctionReflections($functionReflector);
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
    public function getInterfaceReflections()
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

        return array_map(function (ReflectionClass $betterClassReflection) {
            return $this->transformerCollector->transformReflectionToElement($betterClassReflection);
        }, $betterClassReflections);
    }

    /**
     * @param object[] $classInterfaceAndTraitReflections
     */
    private function separateClassInterfaceAndTraitReflections($classInterfaceAndTraitReflections): void
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

        return array_map(function (ReflectionFunction $betterFunctionReflection) {
            return $this->transformerCollector->transformReflectionToElement($betterFunctionReflection);
        }, $betterFunctionReflections);
    }
}
