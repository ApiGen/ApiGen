<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php54\Traits;


class TheWorldIsNotEnough
{
	use HelloWorld;


	public function sayHello()
	{
		echo 'Hello Universe!';
	}
}
