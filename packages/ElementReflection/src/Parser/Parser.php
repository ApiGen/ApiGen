<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Parser;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
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
     * @var ReflectionFunction[]
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
        $this->classReflections = $this->transformBetterClassReflections($classReflector);

        $functionReflector = new FunctionReflector($directoriesSourceLocator);
        $this->functionReflections = $this->transformBetterFunctionReflections($functionReflector);

        // @todo constants
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassReflections(): array
    {
        return $this->classReflections;
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
    private function transformBetterClassReflections(ClassReflector $classReflector): array
    {
        $betterClassReflections = $classReflector->getAllClasses();

        return array_map(function (ReflectionClass $betterClassReflection) {
            return $this->transformerCollector->transformReflectionToElement($betterClassReflection);
        }, $betterClassReflections);
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
