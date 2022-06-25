<?php declare(strict_types = 1);

namespace ApiGenX\Index;

use ApiGenX\Info\ClassLikeInfo;


class FileIndex
{
	/** @var ClassLikeInfo[] indexed by [classLikeName] */
	public array $classLike = [];


	public function __construct(
		public string $name,
		public bool $primary,
	) {
	}
}
