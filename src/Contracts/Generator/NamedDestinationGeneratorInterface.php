<?php declare(strict_types=1);

namespace ApiGen\Contracts\Generator;

interface NamedDestinationGeneratorInterface extends GeneratorInterface
{
    public function getDestinationPath(string $element): string;
}
