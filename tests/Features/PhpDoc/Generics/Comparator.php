<?php declare(strict_types = 1);

namespace ApiGenTests\Features\PhpDoc\Generics;


/**
 * @template-contravariant T
 */
interface Comparator
{
	/**
	 * @param T $a
	 * @param T $b
	 */
	public function compare($a, $b): int;
}
