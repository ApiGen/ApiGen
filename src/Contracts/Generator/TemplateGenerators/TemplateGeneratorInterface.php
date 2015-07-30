<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Generator\TemplateGenerators;

interface TemplateGeneratorInterface
{

    /**
     * Generate template to file
     */
    public function generate();
}
