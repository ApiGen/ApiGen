<?php declare(strict_types=1);

namespace ApiGen\Contracts\Generator;

use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;

interface GeneratorQueueInterface
{

    /**
     * Adds template generator to the queue.
     */
    public function addToQueue(TemplateGeneratorInterface $templateGenerator): void;


    /**
     * Run generator queue.
     */
    public function run(): void;
}
