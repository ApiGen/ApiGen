<?php declare(strict_types = 1);

namespace ApiGenX\Info\Traits;

use ApiGenX\Info\ClassLikeReferenceInfo;


trait HasDependencies
{
	/** @var ClassLikeReferenceInfo[] indexed by [classLikeName] */
	public array $dependencies = [];
}
