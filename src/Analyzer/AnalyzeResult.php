<?php declare(strict_types = 1);

namespace ApiGenX\Analyzer;

use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Info\ErrorInfo;


final class AnalyzeResult
{
	/** @var ClassLikeInfo[] indexed by [classLikeName] */
	public array $classLike = [];

	/** @var ErrorInfo[][] indexed by [error kind][] */
	public array $error = [];
}
