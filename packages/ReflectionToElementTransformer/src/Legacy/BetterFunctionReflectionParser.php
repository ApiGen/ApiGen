<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Legacy;

use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflection\ReflectionMethod;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;

/**
 * @deprecated Remove after removing old Parser.
 */
final class BetterFunctionReflectionParser
{
    /**
     * @return ReflectionFunction|ReflectionMethod
     */
    public static function parseByNameAndFile(string $functionName, string $fileName)
    {
        $singleFileSourceLocator = new SingleFileSourceLocator($fileName);

        // function
        $functionReflector = new FunctionReflector($singleFileSourceLocator);
        $functionReflections = $functionReflector->getAllFunctions();
        if (count($functionReflections)) {
            foreach ($functionReflections as $functionReflection) {
                if ($functionReflection->getName() === $functionName) {
                    return $functionReflection;
                }
            }
        }

        // return class as well

        // method in class
        $classReflector = new ClassReflector($singleFileSourceLocator);
        $classReflections = $classReflector->getAllClasses();
        $classReflection = array_pop($classReflections);

        foreach ($classReflection->getMethods() as $methodReflection) {
            if ($methodReflection->getName() === $functionName) {
                return $methodReflection;
            }
        }
    }
}
