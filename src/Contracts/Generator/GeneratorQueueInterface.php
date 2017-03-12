<?php

namespace ApiGen\Contracts\Generator;

use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;

interface GeneratorQueueInterface
{

    /**
     * Adds template generator to the queue.
     */
    public function addToQueue(TemplateGeneratorInterface $templateGenerator);


    /**
     * Run generator queue.
     */
    public function run();
}
