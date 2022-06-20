<?php declare(strict_types = 1);

namespace ApiGenX\Info\Expr;

use ApiGenX\Info\ExprInfo;


final class DimFetchExprInfo implements ExprInfo
{
	public function __construct(
		public ExprInfo $expr,
		public ExprInfo $dim,
	) {
	}
}
