<?php declare(strict_types = 1);

namespace ApiGenTests\Features\PhpDoc\Types;

/**
 * @property positive-int $positiveInt
 * @property negative-int $negativeInt
 * @property int<0, 10>   $intRange
 * @property int<min, 0>  $leftUnboundedIntRange
 * @property int<0, max>  $rightUnboundedIntRange
 */
interface IntegerRanges
{
}
