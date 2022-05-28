<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte\Template;


final class TreeTemplate
{
	public function __construct(
		public GlobalParameters $global,
	) {
	}
}
