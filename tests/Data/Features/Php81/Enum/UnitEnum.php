<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php81\Enum;


enum UnitEnum implements Colorful
{
	case Hearts;
	case Diamonds;
	case Clubs;
	case Spades;


	public function color(): string
	{
		return match ($this) {
			self::Hearts, self::Diamonds => 'Red',
			self::Clubs, self::Spades => 'Black',
		};
	}


	public function shape(): string
	{
		return 'Rectangle';
	}
}
