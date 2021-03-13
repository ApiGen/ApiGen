<?php declare(strict_types = 1);

namespace ApiGenX\Templates\ClassicX;

use ApiGenX\Index\Index;
use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassLikeInfo;


final class GlobalParameters
{
	public function __construct(
		public ?Index $index = null,
		public string $title,
		public string $activePage,
		public ?NamespaceIndex $activeNamespace,
		public ?ClassLikeInfo $activeClassLike,
	) {
	}
}
