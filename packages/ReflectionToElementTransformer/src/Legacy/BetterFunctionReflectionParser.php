<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Legacy;

use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;

/**
 * @deprecated Remove after removing old Parser.
 */
final class BetterFunctionReflectionParser
{
    public static function parseByNameAndFile(string $functionName, string $fileName): ReflectionFunction
    {
        $singleFileSourceLocator = new SingleFileSourceLocator($fileName);
        $functionReflector = new FunctionReflector($singleFileSourceLocator);
        $functionReflections = $functionReflector->getAllFunctions();

        foreach ($functionReflections as $functionReflection) {
            if ($functionReflection->getName() === $functionName) {
                return $functionReflection;
            }
        }
    }
}
