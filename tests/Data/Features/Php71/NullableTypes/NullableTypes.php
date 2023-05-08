<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php71\NullableTypes;


class NullableTypes
{
	public ?int $nullableProperty;


	public function __construct(
		public ?string $nullablePromoted,
	) {
	}


	public function test(?array $nullable): ?array
	{
		return $nullable;
	}
}
