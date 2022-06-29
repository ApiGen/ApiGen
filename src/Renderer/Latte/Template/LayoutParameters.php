<?php declare(strict_types = 1);

namespace ApiGen\Renderer\Latte\Template;

use ApiGen\Index\NamespaceIndex;
use ApiGen\Info\ElementInfo;


class LayoutParameters
{
	public function __construct(
		public string $activePage,
		public ?NamespaceIndex $activeNamespace,
		public ?ElementInfo $activeElement,
	) {
	}
}
