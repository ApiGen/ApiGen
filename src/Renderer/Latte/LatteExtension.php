<?php declare(strict_types = 1);

namespace ApiGen\Renderer\Latte;

use Latte;


class LatteExtension extends Latte\Extension
{

	public function getTags(): array
	{
		return [
			'pre' => [LattePreNode::class, 'create'],
		];
	}
}
