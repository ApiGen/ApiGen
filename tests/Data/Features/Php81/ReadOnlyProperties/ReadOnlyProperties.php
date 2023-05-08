<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php81\ReadOnlyProperties;


class ReadOnlyProperties
{
	public readonly int $a;

	public function __construct(
		public readonly string $b,
	) {
	}
}
