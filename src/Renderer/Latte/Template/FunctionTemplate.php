<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte\Template;

use ApiGenX\Index\Index;
use ApiGenX\Info\FunctionInfo;


class FunctionTemplate
{
	public function __construct(
		public Index $index,
		public ConfigParameters $config,
		public LayoutParameters $layout,
		public FunctionInfo $function,
	) {
	}
}
