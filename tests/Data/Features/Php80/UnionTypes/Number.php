<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php80\UnionTypes;


class Number
{
	protected int|float $number;


	public function setNumber(int|float $number): void
	{
		$this->number = $number;
	}


	public function getNumber(): int|float
	{
		return $this->number;
	}
}
