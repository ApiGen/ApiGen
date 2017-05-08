<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\InterfaceReflection\Source;

interface RichInterface extends PoorInterface
{
    public function getSomeStuff();
}
