<?php declare(strict_types = 1);

namespace ApiGenX\Templates\ClassicX;

use ApiGenX\Index\NamespaceIndex;


final class NamespaceTemplate
{
	public function __construct(
		public GlobalParameters $global,
		public NamespaceIndex $namespace,
	) {
	}
}
