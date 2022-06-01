<?php declare(strict_types = 1);

namespace ApiGenX\Info\Expr;

use ApiGenX\Info\ExprInfo;


final class TernaryExprInfo implements ExprInfo
{
	public function __construct(
		public ExprInfo $condition,
		public ?ExprInfo $if,
		public ExprInfo $else,
	) {
	}
}
