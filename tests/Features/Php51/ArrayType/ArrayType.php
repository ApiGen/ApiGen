<?php declare(strict_types = 1);

namespace ApiGenTests\Features\Php51\ArrayType;


class ArrayType
{
	public function test(array $array)
	{
		var_dump($array);
	}
}
