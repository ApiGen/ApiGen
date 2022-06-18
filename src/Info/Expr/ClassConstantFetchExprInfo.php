<?php declare(strict_types = 1);

namespace ApiGenX\Info\Expr;

use ApiGenX\Info\ClassLikeReferenceInfo;
use ApiGenX\Info\ExprInfo;


final class ClassConstantFetchExprInfo implements ExprInfo
{
	public function __construct(
		public ClassLikeReferenceInfo $classLike,
		public string $name,
	) {
	}
}
