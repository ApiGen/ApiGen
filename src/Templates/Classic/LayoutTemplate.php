<?php declare(strict_types = 1);

namespace ApiGenX\Templates\Classic;

use ApiGenX\Index;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\NamespaceIndex;


abstract class LayoutTemplate
{
	public Index $index;

	public ?NamespaceIndex $namespaceIndex = null;

	public ?ClassLikeInfo $classLike = null;
}
