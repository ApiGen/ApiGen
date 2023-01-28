<?php declare(strict_types = 1);

namespace ApiGenTests\Features\PhpDoc\Alias;


/**
 * @phpstan-import-type UserAddress from LocalTypeAlias
 * @phpstan-import-type UserAddress from LocalTypeAlias as DeliveryAddress
 */
class ImportedTypeAlias
{
	/** @var UserAddress */
	public array $a;

	/** @var DeliveryAddress */
	public array $b;
}
