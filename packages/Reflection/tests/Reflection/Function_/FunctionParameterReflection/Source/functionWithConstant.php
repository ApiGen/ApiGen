<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Function_\FunctionParameterReflection\Source;

const HI = 5;

function functionWithConstant(int $hello = HI): void
{
}
