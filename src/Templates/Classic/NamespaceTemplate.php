<?php declare(strict_types = 1);

namespace ApiGenX\Templates\Classic;

use ApiGenX\Index\NamespaceIndex;


final class NamespaceTemplate extends LayoutTemplate
{
	public NamespaceIndex $namespace; // TODO! collision with LayoutTemplate var
}
