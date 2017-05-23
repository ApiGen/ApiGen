<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection\Source;

use Nette\DI\CompilerExtension;

final class SomeClass extends CompilerExtension  implements SomeInterface
{
    public function getSomeStuff(): void
    {
    }

    public function riseAndShine(): void
    {
    }
}
