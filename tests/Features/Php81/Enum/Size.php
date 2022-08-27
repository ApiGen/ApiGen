<?php declare(strict_types = 1);

namespace ApiGenTests\Features\Php81\Enum;


enum Size
{
	case Small;
	case Medium;
	case Large;


	public const Huge = self::Large;


	public static function fromLength(int $cm): self
	{
		return match (true) {
			$cm < 50 => self::Small,
			$cm < 100 => self::Medium,
			default => self::Large,
		};
	}
}
