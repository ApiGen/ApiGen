<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php72\ObjectType;


class ObjectType
{
	public object $objectProperty;


	public function __construct(
		public object $objectPromoted,
	) {
	}


	public function test(object $object): object
	{
		return $object;
	}
}
