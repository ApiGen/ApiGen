<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php82\FetchEnumPropertiesInConstExpr;


class FetchEnumPropertiesInConstExpr
{
	public const KEY = 'va';

	public const COLORS = [
		'blue' => Color::Blue->value,
		'red' => Color::Red?->value,
		'green' => Color::Green->{self::KEY . 'lue'},
	];
}
