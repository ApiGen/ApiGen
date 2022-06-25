<?php declare(strict_types = 1);

namespace ApiGenX\Info\Expr;

use ApiGenX\Info\ExprInfo;


class NullExprInfo implements ExprInfo
{
	public function toString(): string
	{
		return 'null';
	}
}
