<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\PhpDoc\Alias;

use DateTimeImmutable;


/**
 * @phpstan-type UserAddress array{street: string, city: string, zip: DateTimeImmutable}
 */
class LocalTypeAlias
{
	/** @var UserAddress */
	public array $address;
}
