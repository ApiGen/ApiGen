<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte\Template;


class SourceTemplate
{
	public function __construct(
		public GlobalParameters $global,
		public string $path,
		public string $source,
	) {
	}
}
