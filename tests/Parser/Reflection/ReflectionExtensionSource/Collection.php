<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Reflection\ReflectionExtensionSource;

use Countable;

final class Collection implements Countable
{
    public function count(): void
    {
    }
}
