<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php71\VoidType;


class VoidType
{
	public function say(string $name): void
	{
		echo $name;
	}
}
