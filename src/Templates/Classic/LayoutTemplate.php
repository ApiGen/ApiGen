<?php declare(strict_types = 1);

namespace ApiGenX\Templates\Classic;

use ApiGenX\Index\Index;
use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassLikeInfo;


abstract class LayoutTemplate
{
	public Index $index;

	public ?NamespaceIndex $namespaceIndex = null;

	public ?ClassLikeInfo $classLike = null;
}
