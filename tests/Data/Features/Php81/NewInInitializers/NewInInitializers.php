<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php81\NewInInitializers;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;


class NewInInitializers
{
	public function __construct(
		public DateTimeInterface $time = new DateTimeImmutable(
			'now',
			new DateTimeZone('Europe/Prague'),
		),
	) {
	}


	public function test(DateTimeInterface $time = new DateTimeImmutable('today'))
	{
	}
}
