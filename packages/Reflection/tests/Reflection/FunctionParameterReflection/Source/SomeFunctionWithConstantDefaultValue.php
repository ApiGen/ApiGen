<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\FunctionParameterReflection\Source;

define('HI', 5);

//function someAloneFunction(int $hello = SomeClassWithConstants::HEY)
function someAloneFunction(int $hello = HI)
{
}
