<?php declare(strict_types = 1);

namespace ApiGenTests\Features\Php80\ConstructorPromotion;


class Point
{
	public function __construct(
		public float $x = 0.0,
		public float $y = 0.0,
		public float $z = 0.0,
	) {
	}
}
