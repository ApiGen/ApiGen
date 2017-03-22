<?php declare(strict_types=1);

namespace ApiGen\Contracts\Generator;

use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;

interface GeneratorQueueInterface
{
    public function addToQueue(TemplateGeneratorInterface $templateGenerator): void;


    public function run(): void;
}
