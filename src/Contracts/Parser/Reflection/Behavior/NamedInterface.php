<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

interface NamedInterface
{
    public function getName(): string;

    public function getPrettyName(): string;
}
