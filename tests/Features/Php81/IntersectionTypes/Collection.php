<?php declare(strict_types = 1);

namespace ApiGenTests\Features\Php81\IntersectionTypes;

use Countable;
use Traversable;


class Collection
{
	protected Traversable & Countable $items;


	public function setItems(Traversable & Countable $items): void
	{
		$this->items = $items;
	}


	public function getItems(): Traversable & Countable
	{
		return $this->items;
	}
}
