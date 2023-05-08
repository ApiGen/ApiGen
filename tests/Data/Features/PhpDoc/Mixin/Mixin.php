<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\PhpDoc\Mixin;

/**
 * @method string getFoo()
 */
class Mixin
{
	public string $name = 'Hello';


	public function hello(): void
	{
	}
}
