<?php declare(strict_types = 1);

namespace ApiGenTests\Features\Php80\ConstructorPromotion;

use ApiGen\Info\ExprInfo;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;


class ParamNode extends Node
{
	public function __construct(
		public string $name,
		public ?ExprInfo $default = null,
		public ?TypeNode $type = null,
		public bool $byRef = false,
		public bool $variadic = false,
		int $startLoc = null,
		int $endLoc = null,
	) {
		parent::__construct($startLoc, $endLoc);
	}
}
