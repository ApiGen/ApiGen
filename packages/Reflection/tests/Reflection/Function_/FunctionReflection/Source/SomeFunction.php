<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Function_\FunctionReflection\Source;

/**
 * Some description.
 *
 * And more lines!
 *
 * @param int $number
 * @param string|null $name
 * @param string[] $arguments
 * @return string
 */
function someAloneFunction(int $number, ?string $name = null, string ...$arguments): string
{
    return 'hi';
}

function add($a, $b)
{
    return $a + $b;
}
