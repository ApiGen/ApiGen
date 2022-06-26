<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte\Template;

use ApiGenX\Info\FunctionInfo;


class FunctionTemplate
{
	public function __construct(
		public GlobalParameters $global,
		public FunctionInfo $function,
	) {
	}
}
