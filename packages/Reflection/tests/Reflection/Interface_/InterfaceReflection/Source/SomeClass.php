<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection\Source;

use Countable;

final class SomeClass implements SomeInterface, Countable
{
    public function getSomeStuff(): void
    {
    }

    public function riseAndShine(): void
    {
    }

    public function count(): int
    {
        return 11;
    }
}
