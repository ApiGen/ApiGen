<?php declare(strict_types = 1);

namespace ApiGenX\Analyzer;

use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Info\ErrorInfo;
use ApiGenX\Info\FunctionInfo;


class AnalyzeResult
{
	/**
	 * @param ClassLikeInfo[] $classLike indexed by [classLikeName]
	 * @param FunctionInfo[]  $function  indexed by [functionName]
	 * @param ErrorInfo[][]   $error     indexed by [errorKind][]
	 */
	public function __construct(
		public array $classLike,
		public array $function,
		public array $error,
	) {
	}
}
