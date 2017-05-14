<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Function_\FunctionReflection\Source;

/**
 * Some description.
 *
 * And more lines!
 *
 *
 */
function someAloneFunction(int $number, ?string $name = null, string ...$arguments): string
{
    return 'hi';
}
