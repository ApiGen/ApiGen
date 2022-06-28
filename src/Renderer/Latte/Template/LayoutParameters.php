<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte\Template;

use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ElementInfo;


class LayoutParameters
{
	public function __construct(
		public string $activePage,
		public ?NamespaceIndex $activeNamespace,
		public ?ElementInfo $activeElement,
	) {
	}
}
