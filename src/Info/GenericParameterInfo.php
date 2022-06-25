<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use PHPStan\PhpDocParser\Ast\Type\TypeNode;


class GenericParameterInfo
{
	public function __construct(
		public string $name,
		public GenericParameterVariance $variance,
		public ?TypeNode $bound,
		public string $description = '',
	) {
	}
}
