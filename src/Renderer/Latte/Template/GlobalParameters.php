<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte\Template;

use ApiGenX\Index\Index;
use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassLikeInfo;


class GlobalParameters
{
	public function __construct(
		public Index $index,
		public string $title,
		public string $activePage,
		public ?NamespaceIndex $activeNamespace,
		public ?ClassLikeInfo $activeClassLike,
	) {
	}
}
