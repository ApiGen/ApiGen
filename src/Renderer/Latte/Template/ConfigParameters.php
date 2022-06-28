<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte\Template;


class ConfigParameters
{
	public function __construct(
		public string $title,
		public string $version,
	) {
	}
}
