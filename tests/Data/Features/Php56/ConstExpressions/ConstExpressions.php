<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php56\ConstExpressions;


class ConstExpressions
{
	const FOO = 1 + 1;
	const BAR = 1 << 1;
	const GREETING = 'HELLO';
	const BAZ = self::GREETING . ' WORLD!';

	/** @var int */
	public $foo = 1 + 1;

	/** @var array */
	public $bar = [
		1 + 1,
		1 << 2,
		self::BAZ => 'foo ' . 'bar',
		7 - 3,
		6 / 2,
		10 % 3,
		1 <=> 2,
		self::GREETING[0],
		self::BAR ?: 100,
		+1,
		-1,
		PHP_VERSION_ID,
	];

	/** @var string */
	public $baseDir = __DIR__ . '/base';


	/**
	 * @param  int $a
	 * @param  int $b
	 * @param  int $c
	 * @return void
	 */
	public function foo($a = 1 + 1, $b = 2 << 3, $c = self::BAR ? 10 : 100)
	{
	}
}
