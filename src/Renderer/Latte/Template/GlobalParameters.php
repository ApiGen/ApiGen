<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte\Template;

use ApiGenX\Index\Index;
use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ElementInfo;


class GlobalParameters
{
	public function __construct(
		public Index $index,
		public string $title,
		public string $version,
		public string $activePage,
		public ?NamespaceIndex $activeNamespace,
		public ?ElementInfo $activeElement,
	) {
	}
}
