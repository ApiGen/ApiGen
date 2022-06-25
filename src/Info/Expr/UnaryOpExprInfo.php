<?php declare(strict_types = 1);

namespace ApiGenX\Info\Expr;

use ApiGenX\Info\ExprInfo;


class UnaryOpExprInfo implements ExprInfo
{
	public function __construct(
		public string $op,
		public ExprInfo $expr,
	) {
	}
}
