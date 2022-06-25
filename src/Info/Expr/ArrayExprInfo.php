<?php declare(strict_types = 1);

namespace ApiGenX\Info\Expr;

use ApiGenX\Info\ExprInfo;


class ArrayExprInfo implements ExprInfo
{
	/**
	 * @param ArrayItemExprInfo[] $items
	 */
	public function __construct(
		public array $items,
	) {
	}
}
