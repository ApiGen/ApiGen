<?php declare(strict_types = 1);

namespace ApiGen\Info\Expr;

use ApiGen\Info\ExprInfo;

use function dechex;
use function ltrim;
use function mb_ord;
use function ord;
use function preg_match;
use function preg_replace_callback;
use function str_pad;
use function strlen;
use function strtoupper;

use const STR_PAD_LEFT;


class StringExprInfo implements ExprInfo
{
	public function __construct(
		public string $value,
		public ?string $raw,
	) {
	}


	public function toString(): string
	{
		$utf8 = (bool) preg_match('##u', $this->value);
		$pattern = $utf8 ? '#[\p{C}\\\\]#u' : '#[\x00-\x1F\x7F-\xFF\\\\]#';
		$special = ["\r" => '\r', "\n" => '\n', "\t" => '\t', '\\' => '\\\\'];

		$s = preg_replace_callback(
			$pattern,
			function ($m) use ($special) {
				if (isset($special[$m[0]])) {
					return $special[$m[0]];

				} elseif (strlen($m[0]) === 1) {
					return '\x' . str_pad(strtoupper(dechex(ord($m[0]))), 2, '0', STR_PAD_LEFT);

				} else {
					return '\u{' . strtoupper(ltrim(dechex(mb_ord($m[0])), '0')) . '}';
				}
			},
			$this->value,
		);

		return '"' . $s . '"';
	}
}
