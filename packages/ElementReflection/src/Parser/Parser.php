<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Parser;

use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\ReflectionToElementTransformer\Contract\TransformerCollectorInterface;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;

final class Parser
{
    /**
     * @var ReflectionClass[]
     */
    private $classReflections = [];

    /**
     * @var ReflectionFunction[]
     */
    private $functionReflections = [];

    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

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
        $this->classReflections = $classReflector->getAllClasses();

        $functionReflector = new FunctionReflector($directoriesSourceLocator);

        $this->functionReflections = array_map(function (ReflectionFunction $functionReflection) {
            return $this->transformerCollector->transformReflectionToElement($functionReflection);
        }, $functionReflector->getAllFunctions());

        // @todo constants
    }

    /**
     * @return ReflectionClass[]
     */
    public function getClassReflections(): array
    {
        return $this->classReflections;
    }

    /**
     * @return ReflectionFunction[]
     */
    public function getFunctionReflections(): array
    {
        return $this->functionReflections;
    }
}
