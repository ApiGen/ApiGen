<?php declare(strict_types = 1);

namespace ApiGenTests\Features\Php54\CallableType;


class CallableType
{
	public function test(callable $callable)
	{
		$callable();
	}
}
