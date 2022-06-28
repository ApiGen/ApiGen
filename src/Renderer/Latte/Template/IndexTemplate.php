<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte\Template;

use ApiGenX\Index\Index;


class IndexTemplate
{
	public function __construct(
		public Index $index,
		public ConfigParameters $config,
		public LayoutParameters $layout,
	) {
	}
}
