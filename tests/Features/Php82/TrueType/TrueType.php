<?php declare(strict_types = 1);

namespace ApiGenTests\Features\Php82\TrueType;


class TrueType
{
	public function test(true $value): true
	{
		return $value;
	}
}
