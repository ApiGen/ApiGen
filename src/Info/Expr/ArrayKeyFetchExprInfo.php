<?php declare(strict_types = 1);

namespace ApiGenX\Info\Expr;

use ApiGenX\Info\ExprInfo;


final class ArrayKeyFetchExprInfo implements ExprInfo
{
	public function __construct(
		public ExprInfo $array,
		public ExprInfo $key,
	) {
	}
}
