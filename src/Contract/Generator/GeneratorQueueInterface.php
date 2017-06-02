<?php declare(strict_types=1);

namespace ApiGen\Contract\Generator;

interface GeneratorQueueInterface
{
    public function addGenerator(GeneratorInterface $generator): void;

    public function run(): void;
}
