<?php declare(strict_types = 1);

namespace ApiGenTests\Features\Php74\TypedProperties;

use DateTimeInterface;


trait TypedPropertiesTrait
{
	public bool $bool = true;

	public ?bool $boolNullable;

	public int $int = 123;

	public ?int $intNullable;

	public float $float = 1.23;

	public ?float $floatNullable;

	public string $string;

	public ?string $stringNullable;

	public array $array;

	public ?array $arrayNullable;

	public object $object;

	public ?object $objectNullable;

	public iterable $iterable;

	public ?iterable $iterableNullable;

	public DateTimeInterface $dateTime;

	public ?DateTimeInterface $dateTimeNullable;

	public self $self;

	public ?self $selfNullable;

	public parent $parent;

	public ?parent $parentNullable;
}
