<?php declare(strict_types = 1);

namespace ApiGenX\Info\Expr;

use ApiGenX\Info\ExprInfo;


final class FloatExprInfo implements ExprInfo
{
	public function __construct(
		public float $value,
	) {
	}


	public function toString(): string
	{
		if (!is_finite($this->value)) {
			return (string) $this->value;
		}

		$json = json_encode($this->value, JSON_THROW_ON_ERROR);
		return str_contains($json, '.') ? $json : "$json.0";
	}
}
