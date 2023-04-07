<?php declare(strict_types = 1);

namespace ApiGenTests\Features\PhpDoc\Types;

/**
 * @property object{a: int}   $a
 * @property object{b: ?int}  $b
 * @property object{c?: int}  $c
 * @property ?object{d: int}  $d
 * @property object{'e': int} $e
 */
interface ObjectShapes
{
}
