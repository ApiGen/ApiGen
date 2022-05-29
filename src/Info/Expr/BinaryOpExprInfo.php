<?php declare(strict_types = 1);

namespace ApiGenX\Info\Expr;

use ApiGenX\Info\ExprInfo;


final class BinaryOpExprInfo implements ExprInfo
{
	public function __construct(
		public string $op,
		public ExprInfo $left,
		public ExprInfo $right,
	) {
	}
}
