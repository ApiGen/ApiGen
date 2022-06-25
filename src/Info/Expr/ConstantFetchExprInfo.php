<?php declare(strict_types = 1);

namespace ApiGenX\Info\Expr;

use ApiGenX\Info\ExprInfo;


class ConstantFetchExprInfo implements ExprInfo
{
	public function __construct(
		public string $name,
	) {
	}
}
