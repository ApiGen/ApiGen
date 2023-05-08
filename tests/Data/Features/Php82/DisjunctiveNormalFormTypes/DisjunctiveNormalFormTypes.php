<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php82\DisjunctiveNormalFormTypes;

use Traversable;
use Countable;


class DisjunctiveNormalFormTypes
{
	public function test((Traversable & Countable) | array $value): void
	{
	}
}
