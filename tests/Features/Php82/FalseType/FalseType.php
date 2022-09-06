<?php declare(strict_types = 1);

namespace ApiGenTests\Features\Php82\FalseType;


class FalseType
{
	public function test(false $value): false
	{
		return $value;
	}
}
