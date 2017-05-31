<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection\Source;

interface RichInterface extends PoorInterface
{
    public function getSomeStuff(): void;
}
