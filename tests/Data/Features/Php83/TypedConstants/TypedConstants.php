<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php83\TypedConstants;


class TypedConstants
{
	public const int INT_CONST = 1;

	public const float FLOAT_CONST = 1.1;

	/** @var list<positive-int> */
	public const array ARRAY_CONST = [self::INT_CONST, 2, 3];
}
