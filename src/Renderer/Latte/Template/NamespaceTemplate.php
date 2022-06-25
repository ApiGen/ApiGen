<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte\Template;

use ApiGenX\Index\NamespaceIndex;


class NamespaceTemplate
{
	public function __construct(
		public GlobalParameters $global,
		public NamespaceIndex $namespace,
	) {
	}
}
