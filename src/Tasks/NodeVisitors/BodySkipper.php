<?php declare(strict_types = 1);

namespace ApiGenX\Tasks\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;


final class BodySkipper extends NodeVisitorAbstract
{
	public function enterNode(Node $node)
	{
		if ($node instanceof Node\FunctionLike && isset($node->stmts)) {
			$node->stmts = [];
//			return NodeTraverser::DONT_TRAVERSE_CHILDREN;
		}

		return null;
	}
}
