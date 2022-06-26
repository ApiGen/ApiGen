<?php declare(strict_types = 1);

namespace ApiGenX\Index;

use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Info\FunctionInfo;


class FileIndex
{
	/** @var ClassLikeInfo[] indexed by [classLikeName] */
	public array $classLike = [];

	/** @var FunctionInfo[] indexed by [functionName] */
	public array $function = [];


	public function __construct(
		public string $name,
		public bool $primary,
	) {
	}
}
