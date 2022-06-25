<?php declare(strict_types = 1);

namespace ApiGenX\Info\Expr;

use ApiGenX\Info\ExprInfo;


class BooleanExprInfo implements ExprInfo
{
	public function __construct(
		public bool $value,
	) {
	}


	public function toString(): string
	{
		return $this->value ? 'true' : 'false';
	}
}
