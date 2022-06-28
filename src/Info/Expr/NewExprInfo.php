<?php declare(strict_types = 1);

namespace ApiGenX\Info\Expr;

use ApiGenX\Info\ClassLikeReferenceInfo;
use ApiGenX\Info\ExprInfo;


class NewExprInfo implements ExprInfo
{
	/**
	 * @param ArgExprInfo[] $args
	 */
	public function __construct(
		public ClassLikeReferenceInfo $classLike,
		public array $args,
	) {
	}
}
