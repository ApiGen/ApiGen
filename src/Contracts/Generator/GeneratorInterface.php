<?php declare(strict_types=1);

namespace ApiGen\Contracts\Generator;

interface GeneratorInterface
{
    public function generate(): void;
}
