<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Parser;

use ApiGen\Parser\Reflection\ReflectionClass;
use BetterReflection\Reflector\ClassReflector;
use BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;

final class Parser
{
    /**
     * @param string[] $directories
     * @return ReflectionClass[]
     */
    public function parseDirectories(array $directories): array
    {
        $directoriesSourceLocator = new DirectoriesSourceLocator($directories);
        $classReflector = new ClassReflector($directoriesSourceLocator);
        return $classReflector->getAllClasses();
    }
}
