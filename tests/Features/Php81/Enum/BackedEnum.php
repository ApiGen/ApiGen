<?php declare(strict_types = 1);

namespace ApiGenTests\Features\Php81\Enum;


enum BackedEnum: string
{
	case Hearts = 'H';
	case Diamonds = 'D';
	case Clubs = 'C';
	case Spades = 'S';
}
