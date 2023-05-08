<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php71\IterableType;


class IterableType
{
	public iterable $iterableProperty;


	public function __construct(
		public iterable $iterablePromoted,
	) {
	}


	public function test(iterable $iterable): iterable
	{
		return $iterable;
	}
}
