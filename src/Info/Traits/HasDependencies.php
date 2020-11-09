<?php declare(strict_types = 1);

namespace ApiGenX\Info\Traits;

use ApiGenX\Info\NameInfo;


trait HasDependencies
{
	/** @var NameInfo[] indexed by [classLikeName] */
	public array $dependencies = [];
}
