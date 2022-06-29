<?php declare(strict_types = 1);

namespace ApiGen\Info\Traits;

use ApiGen\Info\ClassLikeReferenceInfo;


trait HasDependencies
{
	/** @var ClassLikeReferenceInfo[] indexed by [classLikeName] */
	public array $dependencies = [];
}
