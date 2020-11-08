<?php declare(strict_types = 1);

namespace ApiGenX\Info\Traits;


trait HasDependencies
{
	/** @var true[] indexed by [classLikeName] */
	public array $dependencies = [];
}
