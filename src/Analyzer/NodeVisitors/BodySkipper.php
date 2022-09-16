<?php declare(strict_types = 1);

namespace ApiGen\Analyzer\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;


class BodySkipper extends NodeVisitorAbstract
{
	public function enterNode(Node $node)
	{
		// It is not possible to return NodeTraverser::DONT_TRAVERSE_CHILDREN,
		// because it would break PhpParser\NodeVisitor\NameResolver's resolution of Param nodes.

		if (($node instanceof Node\Stmt\Function_ || $node instanceof Node\Stmt\ClassMethod) && $node->stmts !== null) {
			$node->stmts = [];
		}

		return null;
	}
}
