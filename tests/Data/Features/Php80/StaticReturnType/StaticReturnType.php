<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php80\StaticReturnType;


class StaticReturnType
{
	public int $value = 0;


	public function withValue(int $value): static
	{
		$clone = clone $this;
		$clone->value = $value;

		return $clone;
	}
}
