<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Templating\TemplateFactory;

use ApiGen\Contracts\Templating\Template\TemplateInterface;

interface TemplateFactoryInterface
{

    /**
     * @return TemplateInterface
     */
    public function create();
}
