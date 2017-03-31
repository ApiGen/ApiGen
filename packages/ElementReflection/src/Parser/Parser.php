<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Parser;

use ApiGen\Parser\Reflection\ReflectionClass;
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

    public function processDirectory(string $directory): void
    {
        $directoriesSourceLocator = new DirectoriesSourceLocator([$directory]);

        $classReflector = new ClassReflector($directoriesSourceLocator);
        $this->classReflections = $classReflector->getAllClasses();

        $functionReflector = new FunctionReflector($directoriesSourceLocator);
        $this->functionReflections = $functionReflector->getAllFunctions();
    }

    /**
     * @return ReflectionClass[]
     */
    public function getClasses(): array
    {
        return $this->classReflections;
    }

    /**
     * @return ReflectionFunction[]
     */
    public function getFunctions(): array
    {
        return $this->functionReflections;
    }
}
