<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

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
