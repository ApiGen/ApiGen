<?php declare(strict_types = 1);

namespace ApiGenTests\Features\Php80\ConstructorPromotion;


abstract class Node
{
	public function __construct(
		protected ?int $startLoc = null,
		protected ?int $endLoc = null,
	) {
	}
}
