<?php declare(strict_types = 1);

namespace ApiGenTests\Issues\Issue0981;


class Issue0981
{
	const A = 1;
	const B = 2;
	const C = 3;

	const X = [
		'A' => self::A,
		'B' => self::B,
	];

	const Y = [
		'C' => self::C,
		'X' => self::X,
		'Z' => 123,
	];

	const Z = [
		self::A => self::X,
		self::B => self::Y,
	];
}
