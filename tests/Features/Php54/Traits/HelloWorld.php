<?php declare(strict_types = 1);

namespace ApiGenTests\Features\Php54\Traits;


trait HelloWorld
{
	public function sayHello()
	{
		echo 'Hello World!';
	}


	public function methodFromTrait()
	{
		echo 'Method from trait';
	}
}
