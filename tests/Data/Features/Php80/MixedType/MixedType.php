<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php80\MixedType;


class MixedType
{
	public mixed $mixedProperty;


	public function __construct(
		public mixed $mixedPromoted,
	) {
	}


	public function test(mixed $mixed): mixed
	{
		return $mixed;
	}
}
