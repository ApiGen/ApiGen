<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\PhpDoc\Inline;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;

use function strpos;


/**
 * My favorite links:
 *  - {@link https://example.com}
 *  - {@link https://example.com httpLink}
 *  - {@see DateTimeImmutable}
 *  - {@see DateTimeImmutable classLink}
 *  - {@see DateTimeImmutable::createFromFormat()}
 *  - {@see DateTimeImmutable::createFromFormat() methodLink}
 *  - {@see DateTimeInterface::RFC3339}
 *  - {@see DateTimeInterface::RFC3339 constantLink}
 *  - {@see DateInterval::$days}
 *  - {@see DateInterval::$days propertyLink}
 *  - {@see hello()}
 *  - {@see hello}
 *  - {@see strpos()}
 *  - {@see self}
 *  - {@see self::FOO}
 *  - {@see self::$foo}
 *  - {@see self::foo()}
 *  - {@see self::foo}
 *  - {@see FOO}
 *  - {@see $foo}
 *  - {@see foo}
 *  - {@see foo()}
 */
class InlineLink
{
	public const FOO = 'foo';

	public string $foo = 'bar';

	/**
	 * My favorite links:
	 *  - {@link https://example.com}
	 *  - {@link https://example.com httpLink}
	 *  - {@see DateTimeImmutable}
	 *  - {@see DateTimeImmutable classLink}
	 *  - {@see DateTimeImmutable::createFromFormat()}
	 *  - {@see DateTimeImmutable::createFromFormat() methodLink}
	 *  - {@see DateTimeInterface::RFC3339}
	 *  - {@see DateTimeInterface::RFC3339 constantLink}
	 *  - {@see DateInterval::$days}
	 *  - {@see DateInterval::$days propertyLink}
	 *  - {@see hello()}
	 *  - {@see hello}
	 *  - {@see strpos()}
	 *  - {@see self}
	 *  - {@see self::FOO}
	 *  - {@see self::$foo}
	 *  - {@see self::foo()}
	 *  - {@see self::foo}
	 *  - {@see FOO}
	 *  - {@see $foo}
	 *  - {@see foo}
	 *  - {@see foo()}
	 *
	 * @param int $a Hello {@see DateTimeImmutable} world
	 * @return void Hello {@see DateTimeImmutable} world
	 */
	public function foo(int $a): void
	{
	}
}
