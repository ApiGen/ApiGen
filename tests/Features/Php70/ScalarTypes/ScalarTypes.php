<?php declare(strict_types = 1);

namespace ApiGenTests\Features\Php70\ScalarTypes;


class ScalarTypes
{
	public bool $boolProperty;

	public int $intProperty;

	public float $floatProperty;

	public string $stringProperty;


	public function __construct(
		public bool $boolPromoted,
		public int $intPromoted,
		public float $floatPromoted,
		public string $stringPromoted,
	) {
	}


	public function boolMethod(bool $bool): bool
	{
		return $bool;
	}


	public function intMethod(int $int): int
	{
		return $int;
	}


	public function floatMethod(float $float): float
	{
		return $float;
	}


	public function stringMethod(string $string): string
	{
		return $string;
	}
}
