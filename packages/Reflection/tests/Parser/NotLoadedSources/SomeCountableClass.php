<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser\NotLoadedSources;

use Countable;

final class SomeCountableClass implements Countable
{
    public function count(): int
    {
        return 11;
    }
}
