<?php declare(strict_types = 1);

namespace ApiGenX\Info\Expr;

use ApiGenX\Info\ExprInfo;
use ApiGenX\Info\NameInfo;


final class NewExprInfo implements ExprInfo
{
	/**
	 * @param ArgExprInfo[] $args
	 */
	public function __construct(
		public NameInfo $classLike,
		public array $args,
	) {
	}
}
