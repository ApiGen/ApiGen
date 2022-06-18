<?php declare(strict_types = 1);

namespace ApiGenX\Analyzer\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;


final class BodySkipper extends NodeVisitorAbstract
{
	public function enterNode(Node $node)
	{
		// It is not possible to return NodeTraverser::DONT_TRAVERSE_CHILDREN,
		// because it would break PhpParser\NodeVisitor\NameResolver's resolution of Param nodes.

		if ($node instanceof Node\FunctionLike && isset($node->stmts)) {
			$node->stmts = [];
		}

		return null;
	}
}
