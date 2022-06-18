<?php declare(strict_types = 1);

namespace ApiGenX\Info\Traits;

use ApiGenX\Info\GenericParameterInfo;


trait HasGenericParameters
{
	/** @var GenericParameterInfo[] indexed by [parameterName] */
	public array $genericParameters = [];
}
